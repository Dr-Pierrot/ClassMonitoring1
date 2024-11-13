<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'section_id',
        'user_id',
        'prelim_performance_task',
        'prelim_quiz',
        'prelim_recitation',
        'midterm_performance_task',
        'midterm_quiz',
        'midterm_recitation',
        'finals_performance_task',
        'finals_quiz',
        'finals_recitation',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
