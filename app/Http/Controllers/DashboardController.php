<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // For admin, calculate the total count across all users
            $studentsCount = Student::count();
            $subjectsCount = Subject::count();
            $sectionsCount = Section::count();

            return view('dashboard', compact('studentsCount', 'subjectsCount', 'sectionsCount'));
        } else {
            // For teachers, calculate the count only for the logged-in teacher's data
            $studentsCount = Student::where('user_id', $user->id)->count();
            $query = "SELECT user_subject.id, user_subject.subject_id, user_subject.user_id, subjects.course_code, subjects.name FROM user_subject 
                JOIN subjects ON user_subject.subject_id = subjects.id 
                WHERE user_subject.user_id = ?";
            $subjects = DB::select($query, [$user->id]);
            $subjectsCount = count($subjects);
            $sectionsCount = Section::where('user_id', $user->id)->count();

            return view('dashboard', compact('studentsCount', 'subjectsCount', 'sectionsCount'));
        }
    }
}
