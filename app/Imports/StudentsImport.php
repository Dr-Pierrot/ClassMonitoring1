<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\ClassCard;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class StudentsImport implements ToCollection, WithHeadingRow
{
    protected $userId;
    protected $sectionId;
    protected $subjectId;

    public function __construct($userId, $sectionId, $subjectId)
    {
        $this->userId = $userId;
        $this->sectionId = $sectionId;
        $this->subjectId = $subjectId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Convert date of birth to YYYY-MM-DD format
            $dateOfBirth = Carbon::createFromFormat('m/d/Y', $row['date_of_birth'])->format('Y-m-d');
            
            // Create a new student entry, allowing the same student number in different sections or subjects
            $student = Student::create([
                'student_number' => $row['student_number'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'middle_name' => $row['middle_name'],
                'date_of_birth' => $dateOfBirth,
                'gender' => $row['gender'],
                'course' => $row['course'],
                'section_id' => $this->sectionId,
                'subject_id' => $this->subjectId,
                'user_id' => $this->userId,
                'student_type' => $row['student_type'], // Add student_type
            ]);

            // Create ClassCard for the student with only essential fields
            ClassCard::create([
                'student_id' => $student->id,
                'section_id' => $this->sectionId,
                'subject_id' => $this->subjectId,
            ]);
        }
    }
}
