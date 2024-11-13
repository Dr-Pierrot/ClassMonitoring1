<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_card_id',
        'student_id',
        'type',
        'item',
        'score',
        'over_score',
        'term',
    ];

    // Define relationship with ClassCard
    public function classCard()
    {
        return $this->belongsTo(ClassCard::class);
    }

    // Define relationship with Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Accessor for type
    public function getTypeAttribute($value)
    {
        $types = [
            1 => 'performance_task',
            2 => 'quiz',
            3 => 'recitation',
            4 => 'lec',
            5 => 'lab'
        ];

        return $types[$value] ?? 'unknown';
    }

    // Accessor for term
    public function getTermAttribute($value)
    {
        $terms = [
            1 => 'prelim',
            2 => 'midterm',
            3 => 'finals'
        ];

        return $terms[$value] ?? 'unknown';
    }
}
