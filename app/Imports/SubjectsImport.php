<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubjectsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Map the row data to the model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Subject([
            'course_code' => $row['course_code'], // Ensure that 'course_code' matches the heading in your file
            'name' => $row['name'],               // Make sure the heading corresponds to the columns in your file
            'user_id' => auth()->id(),           // Optionally, associate user_id with the authenticated user
        ]);
    }

    /**
     * Validation rules for the imported data.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'course_code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string', // Assuming description is optional
        ];
    }

    /**
     * Handle row validation error (optional).
     *
     * @param \Maatwebsite\Excel\Validators\Failure[] $failures
     */
    public function onFailure($failures)
    {
        // Handle failures, if needed. You can log or show validation errors
        foreach ($failures as $failure) {
            // Example: Log failure messages
            \Log::error("Row validation failed: " . $failure->errors());
        }
    }
}
