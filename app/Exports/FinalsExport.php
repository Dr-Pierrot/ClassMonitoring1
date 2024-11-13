<?php

namespace App\Exports;

use App\Models\ClassCard;
use App\Models\Score;
use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinalsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    protected $teacherId;
    protected $subjectId;

    public function __construct($teacherId, $subjectId = null)
    {
        $this->teacherId = $teacherId;
        $this->subjectId = $subjectId; // Save the subject ID for use in the export
    }

    public function collection()
    {
        $students = \DB::table('students')
            ->join('class_cards', 'students.id', '=', 'class_cards.student_id')
            ->join('sections', 'students.section_id', '=', 'sections.id')
            ->where('sections.user_id', $this->teacherId) // Ensure the section is associated with the teacher
            ->select(
                'students.id',
                'students.student_number',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.course',
                'sections.name as section_name',
                'class_cards.id as class_card_id'
            )
            ->where('class_cards.subject_id', $this->subjectId)
            ->orderBy('sections.id', 'ASC')
            ->get();

        return $students->map(function ($student) {
            // Fetch scores based on the class card ID
            $classCard = ClassCard::find($student->class_card_id);
            $scores = $classCard 
                ? Score::where('class_card_id', $classCard->id)->get()->groupBy('term') 
                : collect(); // Return an empty collection if no class card found

            // Calculate attendance
            $attendancePresent = $this->calculateAttendance($student->id);
            $attendanceTotal = $this->calculateTotalAttendance($student->id);

            $subjectName = $classCard ? $classCard->subject->name : 'N/A';

            // Calculate the raw scores
            $rawPerformanceTaskScore = $this->calculateScoreSum($scores, 'performance_task', 'finals', 'score');
            $rawQuizScore = $this->calculateScoreSum($scores, 'quiz', 'finals', 'score');
            $rawRecitationScore = $this->calculateScoreSum($scores, 'recitation', 'finals', 'score');
            $rawExamScoreLec = $this->calculateScoreSum($scores, 'lec', 'finals', 'score');
            $rawExamScoreLab = $this->calculateScoreSum($scores, 'lab', 'finals', 'score');

            return [
                'Student Number' => $student->student_number,
                'First Name' => $student->first_name,
                'Middle Name' => $student->middle_name,
                'Last Name' => $student->last_name,
                'Course' => $student->course,
                'Section' => $student->section_name,
                'Subject' => $subjectName,
                'Raw Performance Task Score' => number_format($rawPerformanceTaskScore, 2),
                'Raw Quiz Score' => number_format($rawQuizScore, 2),
                'Raw Recitation Score' => number_format($rawRecitationScore, 2),
                'Raw Lecture Exam Score' => number_format($rawExamScoreLec, 2),
                'Raw Lab Exam Score' => number_format($rawExamScoreLab, 2),
                // Add other necessary fields here
            ];
        });
    }

    protected function calculateAttendance($studentId)
    {
        return Attendance::where('student_id', $studentId)
            ->where('status', 1)
            ->whereIn('type', [1, 2]) // Combined attendance types
            ->count();
    }

    protected function calculateTotalAttendance($studentId)
    {
        return Attendance::where('student_id', $studentId)
            ->whereIn('type', [1, 2]) // Combined attendance types
            ->count();
    }

    protected function calculateScoreSum($scores, $type, $term, $look)
    {
        return $scores->where('type', $type)->where('term', $term)->sum($look);
    }

    public function headings(): array
    {
        return [
            'Student Number',
            'First Name',
            'Middle Name',
            'Last Name',
            'Course',
            'Section',
            'Subject',
            'Raw Performance Task Score',
            'Raw Quiz Score',
            'Raw Recitation Score',
            'Raw Lecture Exam Score',
            'Raw Lab Exam Score'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Finals Raw Scores'; // Custom sheet name
    }
}
