<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ClassCard;
use App\Models\Score;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ClassCardController extends Controller
{
    public function index(Request $request)
    {
        // Get the authenticated teacher's user_id
        $teacherId = auth()->user()->id;
        $subjectId = $request->input('subject_id');

        // Check if there are any students associated with the teacher
        $students = Student::where('user_id', $teacherId)
        ->orderBy('id')->get();

        // Get enrolled students for the specific subject taught by the teacher
        $enrolledStudents = ClassCard::with('student', 'subject')
        ->where('subject_id', $subjectId)
        ->whereHas('student', function ($query) use ($teacherId) {
            $query->where('user_id', $teacherId);
        })
        ->get();

        // If no enrolled students are found, redirect back with an error message
        if ($enrolledStudents->isEmpty()) {
            return redirect()->back()->with('error', 'There are no enrolled students for this subject yet.');
        }

        // Retrieve the student_id from the request, if not provided, get the first student's ID
        $student_id = $request->input('student_id') ?? $enrolledStudents[0]->student->id;

        // Fetch the student, ensuring the student belongs to the authenticated teacher
        $student = $students->find($student_id);
        if (!$student) {
            return redirect()->route('class-card.index')->with('error', 'Student not found.');
        }

        $subjectName = Subject::find($subjectId)->name;
        
        $sections = Section::where('user_id', $teacherId)->get();
        
        // Fetch the class card for the student
        $classCard = ClassCard::where('student_id', $student->id)->where('subject_id', $subjectId)->first();

        // Retrieve scores and group them by term, ensure classCard exists to avoid null references
        $scores = $classCard 
            ? Score::where('class_card_id', $classCard->id)->get()->groupBy('term') 
            : collect(); // Return an empty collection if no class card found

        // Initialize the 'prelim', 'midterm', and 'finals' terms
        $scores = $scores->put('prelim', $scores->get('prelim', collect())); 
        $scores = $scores->put('midterm', $scores->get('midterm', collect())); 
        $scores = $scores->put('finals', $scores->get('finals', collect())); 

        $totalScore = $scores->put('prelim', $scores->get('prelim', collect())); 
        $totalScore = $scores->put('midterm', $scores->get('midterm', collect())); 
        $totalScore = $scores->put('finals', $scores->get('finals', collect())); 

        $attendancePresent = 
            Attendance::where('subject_id', $subjectId)->where('type', 1)->where('status', 1)->where('student_id', $student_id)->count() +
            Attendance::where('subject_id', $subjectId)->where('type', 2)->where('status', 1)->where('student_id', $student_id)->count();
        $attendanceTotal = 
            Attendance::where('subject_id', $subjectId)->where('type', 1)->where('student_id', $student_id)->count() +
            Attendance::where('subject_id', $subjectId)->where('type', 2)->where('student_id', $student_id)->count();


        // Get all student IDs that belong to the teacher
        $studentIds = $enrolledStudents->pluck('student_id')->toArray();

        // Determine previous and next student IDs
        $currentIndex = array_search($student_id, $studentIds);
        $prevStudentId = $currentIndex > 0 ? $studentIds[$currentIndex - 1] : null;
        $nextStudentId = $currentIndex < count($studentIds) - 1 ? $studentIds[$currentIndex + 1] : null;

        $selected_exam_type = $request->input('selected_exam_type');
        // return $scores;
        // Pass data to the view
        return view('class_card.index', compact('attendancePresent', 'attendanceTotal', 'students', 'enrolledStudents', 'subjectName', 'sections', 'student', 'classCard', 'scores', 'totalScore', 'prevStudentId', 'nextStudentId', 'subjectId', 'selected_exam_type'));
    }

    public function performanceTaskStore(Request $request)
    {
        // Basic validation rules
        $request->validate([
            'class_card_id' => 'required|exists:class_cards,id', // Validate that the class card exists
            'student_id' => 'required|exists:students,id', // Validate that the student exists
            'score' => 'required|numeric|min:0|max:100', // Score validation
            'over_score' => 'required|numeric|min:0|max:100', // Over score validation
            'term' => 'required|in:1,2,3', // Only allow specified terms
        ]);

        // Check if 'over_score' is not lower than 'score'
        if ($request->score > $request->over_score) {
            return redirect()->back()->withErrors([
                'over_score' => 'The over score must be greater than or equal to the score.'
            ])->withInput();
        }

        // Determine the next item number for the same class card, term, and type of activity
        $lastItem = Score::where('class_card_id', $request->class_card_id)
            ->where('term', $request->term)
            ->where('type', $request->type_activity)
            ->max('item');

        // Set the item number (start with 1 if none exists)
        $nextItem = $lastItem ? $lastItem + 1 : 1;

        // Save score for the selected student
        $score = new Score();
        $score->class_card_id = $request->class_card_id;
        $score->student_id = $request->student_id;
        $score->score = $request->score;
        $score->over_score = $request->over_score; // Add over_score to the model
        $score->type = $request->type_activity; // Set type for performance task
        $score->item = $nextItem; // Set the item number
        $score->term = $request->term; // Set the term
        $score->save();

        // Get the class card details to find other students in the same subject and section
        $classCard = ClassCard::find($request->class_card_id);

        // Fetch other students' class cards with the same subject, section, and teacher (user_id)
        $other_class_cards = ClassCard::where('subject_id', $classCard->subject_id)
            ->where('section_id', $classCard->section_id)
            ->where('user_id', $classCard->user_id) // Teacher ID
            ->where('id', '!=', $request->class_card_id) // Exclude the current student's class card
            ->get();

        // Add a score of 0 for each of these other students if they don't have an existing score for the same term, type, and item
        foreach ($other_class_cards as $other_class_card) {
            // Check if this student already has a score for the same term, activity type, and item
            $existing_score = Score::where('class_card_id', $other_class_card->id)
                ->where('term', $request->term)
                ->where('type', $request->type_activity)
                ->where('item', $nextItem) // Ensure we're checking by item number as well
                ->first();

            // Only create a score if none exists yet
            if (!$existing_score) {
                $other_score = new Score();
                $other_score->class_card_id = $other_class_card->id; // Use the other student's class card ID
                $other_score->student_id = $other_class_card->student_id; // Use the other student's ID
                $other_score->score = 0; // Set score to 0
                $other_score->over_score = $request->over_score; // Same over_score as the current student
                $other_score->type = $request->type_activity; // Same type of activity
                $other_score->item = $nextItem; // Set the same item number
                $other_score->term = $request->term; // Same term
                $other_score->save();
            }
        }

        return redirect()->back()->with('success', 'Score saved successfully for all students.')
        ->with('selected_exam_type', $request->term);
    }


    public function performanceTaskUpdate(Request $request, Score $score)
    {
        // Define custom arrays for types and terms
        $types = [
            1 => 'performance_task',
            2 => 'quiz',
            3 => 'recitation',
            4 => 'lec',
            5 => 'lab'
        ];

        $terms = [
            1 => 'prelim',
            2 => 'midterm',
            3 => 'finals'
        ];

        // Validate the score and over_score
        $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:100', function($attribute, $value, $fail) use ($score) {
                if ($value > $score->over_score) {
                    $fail('The score must not exceed the over score of ' . $score->over_score . '.');
                }
            }],
            'over_score' => 'required|numeric|min:0|max:100', // Validate the over_score
        ]);

        // Update the current score
        $score->update([
            'score' => $request->score,
            'over_score' => $request->over_score, // Update the over_score of the current score
        ]);

        // Store the current numeric term and type for further checks
        $currentTermKey = array_search($score->type, $types); // Get the numeric type key from the types array
        $currentTypeKey = array_search($score->term, $terms); // Get the numeric term key from the terms array
        $currentItem = $score->item;

        // Update the over_score for other scores with the same term, type, and item
        Score::where('term', $currentTypeKey) // Use the numeric term directly
            ->where('type', $currentTermKey) // Use the numeric type directly
            ->where('item', $currentItem)
            ->where('id', '!=', $score->id) // Exclude the current score from being updated
            ->update(['over_score' => $request->over_score]); // Update the over_score for all matching scores

        return redirect()->back()->with('success', 'Score updated successfully.');
    }


    public function performanceTaskBulkDelete(Request $request)
    {
        // Validate the incoming request to ensure all required data is present
        $request->validate([
            'type' => 'required|numeric',
            'term' => 'required|numeric',
            'item' => 'required|numeric',
        ]);

        // Find and delete all matching scores based on the criteria
        $deletedRows = Score::where('type', $request->type)
                            ->where('term', $request->term)
                            ->where('item', $request->item)
                            ->delete();

        // Check if any rows were actually deleted
        if ($deletedRows > 0) {
            return response()->json(['message' => 'Score deleted successfully.'], 200);
        } else {
            return response()->json(['message' => 'No matching score found.'], 404);
        }
    }




    public function filterStudents(Request $request)
    {
        $subjectId = $request->input('subject_id');
        $sectionId = $request->input('section_id');
    
        // Fetch students based on the selected subject and section, load their related section and subject
        $students = ClassCard::with(['student', 'section.subject']) // Load section and its related subject
                    ->whereHas('subject', function ($query) use ($subjectId) {
                        if ($subjectId) {
                            $query->where('id', $subjectId);
                        }
                    })
                    ->whereHas('section', function ($query) use ($sectionId) {
                        if ($sectionId) {
                            $query->where('id', $sectionId);
                        }
                    })
                    ->get()
                    ->pluck('student');
    
        return response()->json($students);
    }

    public function getScoresApi()
    {
        // Fetch scores based on subject_id and type
        $scores = Score::get();

        if ($scores->isEmpty()) {
            
            return response()->json([ 
                'success' => false, 
                'message' => 'No scores found'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Scores retrieved successfully',
            'scores' => $scores
        ]);
    }

    public function storeRecitationApi(Request $request)
    {
        // Validate the incoming JSON data
        $request->validate([
            '*.class_card_id' => 'required|integer',
            '*.over_score' => 'required|integer',
            '*.score' => 'required|integer',
            '*.student_id' => 'required|integer',
            '*.term' => 'required|integer',
            '*.type' => 'required|integer',
        ]);

        // // Retrieve the JSON data
        $data = $request->json()->all();

        foreach ($data as $item) {
            // Validate that over_score is greater than or equal to score
            if ($item['score'] > $item['over_score']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Over score must be greater than or equal to score.',
                    'data' => $item
                ], 400); // Return a 400 Bad Request
            }

            // Initialize item number
            $itemNumber = 1;

            // Find the next available item number
            while (Score::where('class_card_id', $item['class_card_id'])
                    ->where('student_id', $item['student_id'])
                    ->where('term', $item['term'])
                    ->where('type', $item['type']) // Type for recitation
                    ->where('item', $itemNumber)
                    ->exists()) {
                $itemNumber++;
            }

            // Save the score
            $recitationScore = new Score();
            $recitationScore->class_card_id = $item['class_card_id'];
            $recitationScore->student_id = $item['student_id'];
            $recitationScore->score = $item['score'];
            $recitationScore->over_score = $item['over_score'];
            $recitationScore->term = $item['term'];
            $recitationScore->type = $item['type']; // Type for recitation
            $recitationScore->item = $itemNumber; // Set the next available item number
            $recitationScore->save();

        }
        // Use a database transaction to ensure all-or-nothing behavior

        return response()->json(['message' => 'Recorded Successfully'], 201); // Return a 201 Created status
    }
    
    public function storeScoresApi(Request $request)
    {
        // Determine the next item number for the same class card, term, and type of activity
        $lastItem = Score::where('class_card_id', $request->class_card_id)
            ->where('term', $request->term)
            ->where('type', $request->type)
            ->max('item');

        // Set the item number (start with 1 if none exists)
        $nextItem = $lastItem ? $lastItem + 1 : 1;

        // Save score for the selected student
        $score = new Score();
        $score->class_card_id = $request->class_card_id;
        $score->student_id = $request->student_id;
        $score->score = $request->score;
        $score->over_score = $request->over_score;
        $score->type = $request->type;
        $score->item = $nextItem;
        $score->term = $request->term;
        $score->save();

        // Get the class card details to find other students in the same subject and section
        $classCard = ClassCard::find($request->class_card_id);

        // Fetch other students' class cards with the same subject, section, and teacher (user_id)
        $otherClassCards = ClassCard::where('subject_id', $classCard->subject_id)
            ->where('section_id', $classCard->section_id)
            ->where('user_id', $classCard->user_id)
            ->where('id', '!=', $request->class_card_id)
            ->get();

        // Add a score of 0 for each of these other students if they don't have an existing score for the same term, type, and item
        foreach ($otherClassCards as $otherClassCard) {
            $existingScore = Score::where('class_card_id', $otherClassCard->id)
                ->where('term', $request->term)
                ->where('type', $request->type)
                ->where('item', $nextItem)
                ->first();

            if (!$existingScore) {
                $otherScore = new Score();
                $otherScore->class_card_id = $otherClassCard->id;
                $otherScore->student_id = $otherClassCard->student_id;
                $otherScore->score = 0;
                $otherScore->over_score = $request->over_score;
                $otherScore->type = $request->type;
                $otherScore->item = $nextItem;
                $otherScore->term = $request->term;
                $otherScore->save();
            }
        }

        // Return a JSON response
        return response()->json([
            'success' => true,
            'message' => 'Score saved successfully for all students.',
        ]);
    }

    public function updateScoreApi(Request $request, $id)
    {
        // Define custom arrays for types and terms
        $types = [
            1 => 'performance_task',
            2 => 'quiz',
            3 => 'recitation',
            4 => 'lec',
            5 => 'lab'
        ];

        $terms = [
            1 => 'prelim',
            2 => 'midterm',
            3 => 'finals'
        ];

        $score = Score::find($id);

        // Update the current score
        $score->update([
            'score' => $request->score,
            'over_score' => $request->over_score, // Update the over_score of the current score
        ]);

        // Store the current numeric term and type for further checks
        $currentTermKey = array_search($score->type, $types); // Get the numeric type key from the types array
        $currentTypeKey = array_search($score->term, $terms); // Get the numeric term key from the terms array
        $currentItem = $score->item;

        // Update the over_score for other scores with the same term, type, and item
        Score::where('term', $currentTypeKey) // Use the numeric term directly
            ->where('type', $currentTermKey) // Use the numeric type directly
            ->where('item', $currentItem)
            ->where('id', '!=', $score->id) // Exclude the current score from being updated
            ->update(['over_score' => $request->over_score]); // Update the over_score for all matching scores

        return response()->json([
            'success' => true,
            'message' => 'Score updated successfully.'
        ]);
    }

    public function scoreDeleteApi(Request $request)
    {
    
        // Find and delete all matching scores based on the criteria
        $deletedRows = Score::where('type', $request->type)
                            ->where('term', $request->term)
                            ->where('item', $request->item)
                            ->delete();

        // Check if any rows were actually deleted
        if ($deletedRows > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Score deleted successfully.']);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No matching score found.']);
        }
    }
}
