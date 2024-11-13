<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubjectsImport; // Import the SubjectsImport class
use Illuminate\Support\Facades\Auth; // Correct import for Auth facade

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::where('user_id', Auth::id())->get();
        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('subjects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Check if the subject already exists
        $subjectExists = Subject::where('course_code', $request->course_code)
            ->where('name', $request->name)
            ->exists();

        if ($subjectExists) {
            return redirect()->back()->with('error', 'Subject already exists.');
        }

        Subject::create([
            'course_code' => $request->course_code,
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
    }

    public function selectSubjects(Request $request)
    {
        // Validate the selected subjects (optional)
        $request->validate([
            'selected_subjects' => 'required|array',  // Ensure subjects are selected
            'selected_subjects.*' => 'exists:subjects,id',  // Validate each subject exists
        ]);

        // Get the selected subjects
        $selectedSubjects = $request->input('selected_subjects');

        // Process the selected subjects (store them, log them, etc.)
        return redirect()->route('subjects.choose')->with('success', 'Subjects selected successfully.');
    }

    // Import method for both file and selected subjects
    public function import(Request $request)
    {
        // Check if a file is uploaded
        if ($request->hasFile('subject_file')) {
            // Handle file import logic
            $request->validate([
                'subject_file' => 'required|mimes:xlsx,xls,csv', // Validate file type
            ]);

            try {
                // Import the file using the SubjectsImport class
                Excel::import(new SubjectsImport, $request->file('subject_file'));

                // Redirect back to the main page after successful import
                return redirect()->route('subjects.index')->with('success', 'Subjects imported successfully from file.');
            } catch (\Exception $e) {
                return redirect()->route('subjects.index')->with('error', 'An error occurred during file import: ' . $e->getMessage());
            }
        }

        // Handle selected subjects import
        if ($request->has('subjects') && is_array($request->input('subjects'))) {
            try {
                // Process the selected subjects
                $selectedSubjects = Subject::whereIn('id', $request->input('subjects'))->get();

                // Example: Log the selected subjects
                \Log::info('Selected subjects for import: ', $selectedSubjects->toArray());

                // Redirect after the import of selected subjects
                return redirect()->route('subjects.index')->with('success', 'Selected subjects imported successfully.');
            } catch (\Exception $e) {
                return redirect()->route('subjects.index')->with('error', 'An error occurred during the selected subjects import: ' . $e->getMessage());
            }
        }

        // If no file or subjects are selected
        return redirect()->route('subjects.index')->with('error', 'Please select subjects or upload a file.');
    }

    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'course_code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($subject->user_id !== Auth::id()) {
            return redirect()->route('subjects.index')->with('error', 'You are not authorized to update this subject.');
        }

        $subjectExists = Subject::where('course_code', $request->course_code)
            ->where('name', $request->name)
            ->exists();

        if ($subjectExists) {
            return redirect()->back()->with('error', 'Subject already exists.');
        }

        $subject->update([
            'course_code' => $request->course_code,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy($id)
{
    // Find the subject by ID
    $subject = Subject::findOrFail($id);

    // Delete the subject
    $subject->delete();

    // Redirect back to the subjects index with a success message
    return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully!');
}


    public function chooseSubjects()
    {
        $subjects = Subject::all();  // Fetch all subjects from the database
        return view('subjects.choose', compact('subjects'));
    }

    public function add(Request $request)
    {
        // Validate the input
        $request->validate([
            'course_code' => 'required|string|max:10',
            'name' => 'required|string|max:255',
        ]);
    
        // Create the new subject with the authenticated user's ID
        $subject = new Subject();
        $subject->course_code = $request->course_code;
        $subject->name = $request->name;
        $subject->user_id = auth()->id(); // This will set the user_id to the currently authenticated user's ID
        $subject->save();
    
        return redirect()->route('subjects.choose')->with('success', 'Subject added successfully!');
    
    }
    public function addSelected(Request $request)
{
    // Validate that subjects are selected
    $request->validate([
        'subjects' => 'required|array|min:1',  // Ensure at least one subject is selected
        'subjects.*' => 'exists:subjects,id',  // Ensure each selected subject exists in the database
    ]);

    // Get the selected subject IDs
    $selectedSubjects = $request->input('subjects');
    
    // Process each selected subject (for example, assign it to a user, enroll a student, etc.)
    foreach ($selectedSubjects as $subjectId) {
        $subject = Subject::find($subjectId);
        // Here, you can do whatever needs to be done with the selected subject, e.g., enrollment
        // Example: Assign subjects to the authenticated user (or any other logic)
        $subject->user_id = auth()->id();  // Just an example, modify according to your business logic
        $subject->save();
    }

    // Redirect back with a success message
    return redirect()->route('subjects.index')->with('success', 'Selected subjects added successfully!');
}

    
}
