<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ClassCardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ChangePasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'loginApi']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/students', [StudentController::class, 'getStudentApi'])->name('api.students.getStudents');
Route::middleware('auth:sanctum')->get('/students', [StudentController::class, 'getStudentApi'])->name('api.students.getStudents');
Route::middleware('auth:sanctum')->get('/students/{id}', [StudentController::class, 'getStudentDetailsApi'])->name('api.students.getDetails');
Route::middleware('auth:sanctum')->post('/students', [StudentController::class, 'storeStudentApi']);
Route::put('students/{student}', [StudentController::class, 'updateStudentDetailsApi']);
Route::middleware('auth:sanctum')->delete('/students/{student}', [StudentController::class, 'destroyStudentApi']);

Route::get('/subjects', [SubjectController::class, 'getSubjectApi'])->name('api.subjects.getSubjects');
Route::middleware('auth:sanctum')->get('/subjects', [SubjectController::class, 'getSubjectApi'])->name('api.subjects.getSubjects');
Route::middleware('auth:sanctum')->get('/subjects/{id}', [SubjectController::class, 'getSubjectDetailsApi'])->name('api.subjects.getDetails');
Route::middleware('auth:sanctum')->post('/subjects', [SubjectController::class, 'storeSubjectApi']);
Route::put('subjects/{subject}', [SubjectController::class, 'updateSubjectDetailsApi']);
Route::middleware('auth:sanctum')->delete('/subjects/{subject}', [SubjectController::class, 'destroySubjectApi']);
Route::middleware('auth:sanctum')->get('/subjects/{subject}/enrolled_students', [SubjectController::class, 'showEnrollApi']);
// Route::get('/subjects/{subject}/enrolled_students', [SubjectController::class, 'showEnrollApi']);
Route::middleware('auth:sanctum')->post('/subjects/enroll', [SubjectController::class, 'enrollStudentsApi']);
Route::middleware('auth:sanctum')->delete('/subjects/unenroll/{enroll}', [SubjectController::class, 'unenrollStudentApi']);
Route::middleware('auth:sanctum')->get('/attendance', [AttendanceController::class, 'getAttendanceApi']);
Route::middleware('auth:sanctum')->get('/attendance/{id}', [AttendanceController::class, 'getAttendanceDetailsApi']);
Route::middleware('auth:sanctum')->post('/attendance', [AttendanceController::class, 'storeAttendanceApi']);
Route::put('attendance/{attendance}', [AttendanceController::class, 'updateAttendanceDetailsApi']);
Route::middleware('auth:sanctum')->delete('/attendance/{attendance}', [AttendanceController::class, 'destroyAttendanceApi']);
Route::middleware('auth:sanctum')->get('/scores', [ClassCardController::class, 'getScoresApi']);
Route::middleware('auth:sanctum')->post('/scores/recitation', [ClassCardController::class, 'storeRecitationApi']);
Route::middleware('auth:sanctum')->post('/scores', [ClassCardController::class, 'storeScoresApi']);
Route::put('/scores/{score}', [ClassCardController::class, 'updateScoreApi']);
Route::middleware('auth:sanctum')->post('/scores/delete', [ClassCardController::class, 'scoreDeleteApi']);

Route::get('/sections', [SectionController::class, 'getSectionApi'])->name('api.sections.getSections');
Route::middleware('auth:sanctum')->get('/sections', [SectionController::class, 'getSectionApi'])->name('api.sections.getSections');
Route::middleware('auth:sanctum')->get('/sections/{id}', [SectionController::class, 'getSectionDetailsApi'])->name('api.sections.getDetails');
Route::middleware('auth:sanctum')->post('/sections', [SectionController::class, 'storeSectionApi']);
Route::put('sections/{section}', [SectionController::class, 'updateSectionDetailsApi']);
Route::middleware('auth:sanctum')->delete('/sections/{section}', [SectionController::class, 'destroySectionApi']);

Route::middleware('auth:sanctum')->post('/changepassword', [ChangePasswordController::class, 'updatePasswordApi']);
