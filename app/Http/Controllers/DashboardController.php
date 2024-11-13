<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Section;
use App\Models\Subject;
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
            $subjectsCount = Subject::where('user_id', $user->id)->count();
            $sectionsCount = Section::where('user_id', $user->id)->count();

            return view('dashboard', compact('studentsCount', 'subjectsCount', 'sectionsCount'));
        }
    }
}
