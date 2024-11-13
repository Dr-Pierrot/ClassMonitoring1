<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'middle_name',
        'date_of_birth',
        'gender',
        'course',
        'user_id',
        'section_id',
        'attendance_id',
        'student_type'
    ];
    
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function enrollments()
    {
        return $this->hasMany(ClassCard::class);
    }
}
