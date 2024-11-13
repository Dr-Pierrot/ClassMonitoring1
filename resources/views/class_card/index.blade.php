@extends('layouts.app')

@section('title', 'Class Card')

@section('content')
<style>
    .square-table td {
        width: 100px;
        height: 100px;
        vertical-align: middle; /* Aligns content vertically */
    }
    .table .hover:hover {
        background-color: wheat;
        cursor: pointer;
    }
</style>
    <div class="container mt-5">
         <!-- Success message -->
         @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <form action="{{ route('class-card.index') }}" method="GET">
            <div class="row mb-4">
                <!-- Student Filter -->
                <input type="hidden" id="subject_id" name="subject_id" value="{{ $subjectId }}">
                <div class="col-md-3">
                    <h3>
                        <strong>{{ $subjectName }}</strong>
                    </h3>
                </div>
                <div class="col-md-3">
                    <label for="section_id">Select Section:</label>
                    <select name="section_id" id="section_id" class="form-control" onchange="filterStudents()">
                        <option value="">All Sections</option>
                        @if(isset($sections))
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : ($student->section->id == $section->id ? 'selected' : '') }}>
                                    {{ $section->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="student_id">Select Student:</label>
                    <select name="student_id" id="student_id" class="form-control">
                        <option value="">Select Student</option>
                        @foreach($enrolledStudents as $enrolled)
                            <option value="{{ $enrolled->student->id }}" {{ request('student_id') == $enrolled->student->id ? 'selected' : ($student->id == $enrolled->student->id ? 'selected' : '') }}>
                                {{ $enrolled->student->first_name }} {{ $enrolled->student->middle_name }} {{ $enrolled->student->last_name }}
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                </div>
            </div>
        </form>
    
        
        <!-- Student Information Section -->
        @if(isset($message))
            <div class="card text-center mt-4">
                <div class="card-body">
                    <h5 class="card-title">No Students Found</h5>
                    <p class="card-text">{{ $message }}</p>
                </div>
            </div>
        @else
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card p-3">
                        <h5 class="mb-3">Student Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> {{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Gender:</strong> {{ $student->gender }}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Course:</strong> {{ $student->course }}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Section:</strong> {{ $student->section->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $selected_exam_type = session('selected_exam_type') != null ? session('selected_exam_type') : '1'; ?>

            <!-- Exam Type Dropdown -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="exam_type">Select Exam Type:</label>
                    <select id="exam_type" class="form-control" onchange="saveExamType()">
                        <option value="1" <?php if ($selected_exam_type === '1') echo 'selected'; ?>>Prelim</option>
                        <option value="2" <?php if ($selected_exam_type === '2') echo 'selected'; ?>>Midterm</option>
                        <option value="3" <?php if ($selected_exam_type === '3') echo 'selected'; ?>>Finals</option>
                    </select>
                </div>
            </div>

            <!-- Class Card Section -->
            <div class="row mb-4" id="examTables">
                <div class="col-md-12">
                    <div class="card p-3">
                        <h5 class="mb-3">Class Card</h5>

                        <div id="prelim-tables" <?php echo $selected_exam_type === '1'? 'style="display: block"' : 'style="display: none"'; ?> class="exam-tables col-md-12">
                            <div class="row">
                                <h3>Prelim</h3>
                                <div class="col-md-12">
                                    <!-- Performance Tasks for Prelim -->
                                    <h6>Performance Tasks</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 1, 1)">Add</td>

                                                            @foreach ($scores->get('prelim')->where('type', 'performance_task') as $performance_task)
                                                                <td class="hover" id="prelim-{{ $performance_task->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $performance_task->id }}, {{ $performance_task->score }}, {{ $performance_task->over_score }})">
                                                                PT {{ $performance_task->item }} <br>{{ $performance_task->score }} / {{ $performance_task->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('prelim')->where('type', 'performance_task')->sum('score')}} / {{$totalScore->get('prelim')->where('type', 'performance_task')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('performance_task', 'prelim')">Remove PT</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <!-- Quizzes for Prelim -->
                                    <h6>Quizzes</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 1, 2)">Add</td>

                                                            @foreach ($scores->get('prelim')->where('type', 'quiz') as $quiz)
                                                                <td class="hover" id="prelim-{{ $quiz->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $quiz->id }}, {{ $quiz->score }}, {{ $quiz->over_score }})">
                                                                Quiz {{ $quiz->item }} <br>{{ $quiz->score }} / {{ $quiz->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('prelim')->where('type', 'quiz')->sum('score')}} / {{$totalScore->get('prelim')->where('type', 'quiz')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('quiz', 'prelim')">Remove Quiz</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-12">
                                    <!-- Recitation for Prelim -->
                                    <h6>Recitation</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <?php echo $scores->get('prelim')->where('type', 'recitation')->isEmpty() ? '<td data-class-card-id="'.$classCard->id.'" data-student-id="'.$student->id.'">No Recitation</td>' : '' ?>

                                                            @foreach ($scores->get('prelim')->where('type', 'recitation') as $recitation)
                                                                <td class="hover" id="prelim-{{ $recitation->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $recitation->id }}, {{ $recitation->score }}, {{ $recitation->over_score }})">
                                                                Recitation {{ $recitation->item }} <br>{{ $recitation->score }} / {{ $recitation->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('prelim')->where('type', 'recitation')->sum('score')}} / {{$totalScore->get('prelim')->where('type', 'recitation')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('recitation', 'prelim')">Remove Recitation</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <h6>Exam</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>Lecture</td>
                                                        @foreach ($scores->get('prelim')->where('type', 'lec') as $exam_prelim)
                                                            <td class="hover" id="prelim-{{ $exam_prelim->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $exam_prelim->id }}, {{ $exam_prelim->score }}, {{ $exam_prelim->over_score }})">
                                                                {{ $exam_prelim->score }} / {{ $exam_prelim->over_score }}
                                                            </td>
                                                        @endforeach
                                                        @for ($i = $scores->get('prelim')->where('type', 'lec')->count(); $i < 1; $i++)
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 1, 4)">Add</td>
                                                        @endfor
                                                    
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>Laboratory</td>
                                                        @foreach ($scores->get('prelim')->where('type', 'lab') as $exam_prelim)
                                                            <td class="hover" id="prelim-{{ $exam_prelim->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $exam_prelim->id }}, {{ $exam_prelim->score }}, {{ $exam_prelim->over_score }})">
                                                                {{ $exam_prelim->score }} / {{ $exam_prelim->over_score }}
                                                            </td>
                                                        @endforeach
                                                        @for ($i = $scores->get('prelim')->where('type', 'lab')->count(); $i < 1; $i++)
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 1, 5)">Add</td>
                                                        @endfor

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="col-md-12">
                                    <h6>Prelim Grade</h6>

                                    @php 
                                        $weight = array(
                                            'performance_task' => 50,
                                            'recitation' => 15,
                                            'quiz' => 25,
                                            'attendance' => 10,
                                        );

                                        $prelimScorePT = $totalScore->get('prelim')->where('type', 'performance_task')->sum('score');
                                        $prelimOverScorePT = $totalScore->get('prelim')->where('type', 'performance_task')->sum('over_score');
                                        $prelimScoreQuiz = $totalScore->get('prelim')->where('type', 'quiz')->sum('score');
                                        $prelimOverScoreQuiz = $totalScore->get('prelim')->where('type', 'quiz')->sum('over_score');
                                        $prelimScoreRecitation = $totalScore->get('prelim')->where('type', 'recitation')->sum('score');
                                        $prelimOverScoreRecitation = $totalScore->get('prelim')->where('type', 'recitation')->sum('over_score');

                                        
                                        $prelimClassStanding = 
                                            (((( $prelimOverScorePT == 0 ? 0:$prelimScorePT / $prelimOverScorePT) * 35 + 65) / 100) * $weight['performance_task'] ) +
                                            (((( $prelimOverScoreQuiz == 0 ? 0:$prelimScoreQuiz / $prelimOverScoreQuiz) * 35 + 65) / 100) * $weight['quiz']) +
                                            (((( $prelimOverScoreRecitation == 0 ? 0:$prelimScoreRecitation / $prelimOverScoreRecitation) * 35 + 65) / 100) * $weight['recitation']) +
                                            (((( $attendanceTotal == 0 ? 0:$attendancePresent / $attendanceTotal) * 35 + 65) / 100) * $weight['attendance'])
                                        ;

                                        $prelimExamScoreLec = $totalScore->get('prelim')->where('type', 'lec')->sum('score');
                                        $prelimExamOverScoreLec = $totalScore->get('prelim')->where('type', 'lec')->sum('over_score');
                                        $prelimExamScoreLab = $totalScore->get('prelim')->where('type', 'lab')->sum('score');
                                        $prelimExamOverScoreLab = $totalScore->get('prelim')->where('type', 'lab')->sum('over_score');

                                        $prelimExam = 
                                            (((( $prelimExamOverScoreLec == 0 ? 0:$prelimExamScoreLec / $prelimExamOverScoreLec) * 35 + 65) / 100) * 50) +
                                            (((( $prelimExamOverScoreLab == 0 ? 0:$prelimExamScoreLab / $prelimExamOverScoreLab) * 35 + 65) / 100) * 50)
                                        ;

                                    @endphp



                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>Grade</td>
                                                        <td><strong>{{ number_format(($prelimExam * 0.6) + ($prelimClassStanding * 0.4), 2) }}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>

                        <div id="midterm-tables" <?php echo $selected_exam_type === '2'? 'style="display: block"' : 'style="display: none"'; ?> class="exam-tables col-md-12">
                            <div class="row">
                                <h3>Midterm</h3>
                                <div class="col-md-12">
                                    <!-- Performance Tasks for Midterm -->
                                    <h6>Performance Tasks</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 2, 1)">Add</td>

                                                            @foreach ($scores->get('midterm')->where('type', 'performance_task') as $performance_task)
                                                                <td class="hover" id="midterm-{{ $performance_task->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $performance_task->id }}, {{ $performance_task->score }}, {{ $performance_task->over_score }})">
                                                                PT {{ $performance_task->item }} <br>{{ $performance_task->score }} / {{ $performance_task->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('midterm')->where('type', 'performance_task')->sum('score')}} / {{$totalScore->get('midterm')->where('type', 'performance_task')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('performance_task', 'midterm')">Remove PT</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                        
                                <div class="col-md-12">
                                    <!-- Quizzes for Midterm -->
                                    <h6>Quizzes</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 2, 2)">Add</td>

                                                            @foreach ($scores->get('midterm')->where('type', 'quiz') as $quiz)
                                                                <td class="hover" id="midterm-{{ $quiz->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $quiz->id }}, {{ $quiz->score }}, {{ $quiz->over_score }})">
                                                                Quiz {{ $quiz->item }} <br>{{ $quiz->score }} / {{ $quiz->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('midterm')->where('type', 'quiz')->sum('score')}} / {{$totalScore->get('midterm')->where('type', 'quiz')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('quiz', 'midterm')">Remove PT</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <!-- Recitation for Midterm -->
                                    <h6>Recitation</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 2, 3)">Add</td>

                                                            @foreach ($scores->get('midterm')->where('type', 'recitation') as $recitation)
                                                                <td class="hover" id="midterm-{{ $recitation->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $recitation->id }}, {{ $recitation->score }}, {{ $recitation->over_score }})">
                                                                Recitation {{ $recitation->item }} <br>{{ $recitation->score }} / {{ $recitation->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('midterm')->where('type', 'recitation')->sum('score')}} / {{$totalScore->get('midterm')->where('type', 'recitation')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('recitation', 'midterm')">Remove Recitation</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <h6>Exam</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>Lecture</td>
                                                        @foreach ($scores->get('midterm')->where('type', 'lec') as $exam_midterm)
                                                            <td class="hover" id="midterm-{{ $exam_midterm->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $exam_midterm->id }}, {{ $exam_midterm->score }}, {{ $exam_midterm->over_score }})">
                                                                {{ $exam_midterm->score }} / {{ $exam_midterm->over_score }}
                                                            </td>
                                                        @endforeach
                                                        @for ($i = $scores->get('midterm')->where('type', 'lec')->count(); $i < 1; $i++)
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 2, 4)">Add</td>
                                                        @endfor
                                                    
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>Laboratory</td>
                                                        @foreach ($scores->get('midterm')->where('type', 'lab') as $exam_midterm)
                                                            <td class="hover" id="midterm-{{ $exam_midterm->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $exam_midterm->id }}, {{ $exam_midterm->score }}, {{ $exam_midterm->over_score }})">
                                                                {{ $exam_midterm->score }} / {{ $exam_midterm->over_score }}
                                                            </td>
                                                        @endforeach
                                                        @for ($i = $scores->get('midterm')->where('type', 'lab')->count(); $i < 1; $i++)
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 2, 5)">Add</td>
                                                        @endfor

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="col-md-12">
                                    <h6>Midterm Grade</h6>

                                    @php 
                                        $weight = array(
                                            'performance_task' => 50,
                                            'recitation' => 15,
                                            'quiz' => 25,
                                            'attendance' => 10,
                                        );

                                        $midtermScorePT = $totalScore->get('midterm')->where('type', 'performance_task')->sum('score');
                                        $midtermOverScorePT = $totalScore->get('midterm')->where('type', 'performance_task')->sum('over_score');
                                        $midtermScoreQuiz = $totalScore->get('midterm')->where('type', 'quiz')->sum('score');
                                        $midtermOverScoreQuiz = $totalScore->get('midterm')->where('type', 'quiz')->sum('over_score');
                                        $midtermScoreRecitation = $totalScore->get('midterm')->where('type', 'recitation')->sum('score');
                                        $midtermOverScoreRecitation = $totalScore->get('midterm')->where('type', 'recitation')->sum('over_score');

                                        
                                        $midtermClassStanding = 
                                            (((( $midtermOverScorePT == 0 ? 0:$midtermScorePT / $midtermOverScorePT) * 35 + 65) / 100) * $weight['performance_task'] ) +
                                            (((( $midtermOverScoreQuiz == 0 ? 0:$midtermScoreQuiz / $midtermOverScoreQuiz) * 35 + 65) / 100) * $weight['quiz']) +
                                            (((( $midtermOverScoreRecitation == 0 ? 0:$midtermScoreRecitation / $midtermOverScoreRecitation) * 35 + 65) / 100) * $weight['recitation']) +
                                            (((( $attendanceTotal == 0 ? 0:$attendancePresent / $attendanceTotal) * 35 + 65) / 100) * $weight['attendance'])
                                        ;

                                        $midtermExamScoreLec = $totalScore->get('midterm')->where('type', 'lec')->sum('score');
                                        $midtermExamOverScoreLec = $totalScore->get('midterm')->where('type', 'lec')->sum('over_score');
                                        $midtermExamScoreLab = $totalScore->get('midterm')->where('type', 'lab')->sum('score');
                                        $midtermExamOverScoreLab = $totalScore->get('midterm')->where('type', 'lab')->sum('over_score');

                                        $midtermExam = 
                                            (((( $midtermExamOverScoreLec == 0 ? 0:$midtermExamScoreLec / $midtermExamOverScoreLec) * 35 + 65) / 100) * 50) +
                                            (((( $midtermExamOverScoreLab == 0 ? 0:$midtermExamScoreLab / $midtermExamOverScoreLab) * 35 + 65) / 100) * 50)
                                        ;

                                    @endphp



                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>Grade</td>
                                                        <td><strong>{{ number_format(($midtermExam * 0.6) + ($midtermClassStanding * 0.4), 2) }}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>

                        <div id="finals-tables" <?php echo $selected_exam_type === '3'? 'style="display: block"' : 'style="display: none"'; ?> class="exam-tables col-md-12"> 
                            <div class="row">
                                <h3>Finals</h3>
                                <div class="col-md-12">
                                    <!-- Performance Tasks for Finals -->
                                    <h6>Performance Tasks</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 3, 1)">Add</td>

                                                            @foreach ($scores->get('finals')->where('type', 'performance_task') as $performance_task)
                                                                <td class="hover" id="finals-{{ $performance_task->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $performance_task->id }}, {{ $performance_task->score }}, {{ $performance_task->over_score }})">
                                                                PT {{ $performance_task->item }} <br>{{ $performance_task->score }} / {{ $performance_task->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('finals')->where('type', 'performance_task')->sum('score')}} / {{$totalScore->get('finals')->where('type', 'performance_task')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('performance_task', 'finals')">Remove PT</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <!-- Quizzes for Finals -->
                                    <h6>Quizzes</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 3, 2)">Add</td>

                                                            @foreach ($scores->get('finals')->where('type', 'quiz') as $quiz)
                                                                <td class="hover" id="finals-{{ $quiz->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $quiz->id }}, {{ $quiz->score }}, {{ $quiz->over_score }})">
                                                                Quiz {{ $quiz->item }} <br>{{ $quiz->score }} / {{ $quiz->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('finals')->where('type', 'quiz')->sum('score')}} / {{$totalScore->get('finals')->where('type', 'quiz')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('quiz', 'finals')">Remove Quiz</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                        
                                <div class="col-md-12">
                                    <!-- Recitation for Finals -->
                                    <h6>Recitation</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 3, 3)">Add</td>

                                                            @foreach ($scores->get('finals')->where('type', 'recitation') as $recitation)
                                                                <td class="hover" id="finals-{{ $recitation->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $recitation->id }}, {{ $recitation->score }}, {{ $recitation->over_score }})">
                                                                Recitation {{ $recitation->item }} <br>{{ $recitation->score }} / {{ $recitation->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('finals')->where('type', 'recitation')->sum('score')}} / {{$totalScore->get('finals')->where('type', 'recitation')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('recitation', 'finals')">Remove Recitation</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <h6>Exam</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>Lecture</td>
                                                        @foreach ($scores->get('finals')->where('type', 'lec') as $exam_finals)
                                                            <td class="hover" id="finals-{{ $exam_finals->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $exam_finals->id }}, {{ $exam_finals->score }}, {{ $exam_finals->over_score }})">
                                                                {{ $exam_finals->score }} / {{ $exam_finals->over_score }}
                                                            </td>
                                                        @endforeach
                                                        @for ($i = $scores->get('finals')->where('type', 'lec')->count(); $i < 1; $i++)
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 3, 4)">Add</td>
                                                        @endfor
                                                    
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>Laboratory</td>
                                                        @foreach ($scores->get('finals')->where('type', 'lab') as $exam_finals)
                                                            <td class="hover" id="finals-{{ $exam_finals->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $exam_finals->id }}, {{ $exam_finals->score }}, {{ $exam_finals->over_score }})">
                                                                {{ $exam_finals->score }} / {{ $exam_finals->over_score }}
                                                            </td>
                                                        @endforeach
                                                        @for ($i = $scores->get('finals')->where('type', 'lab')->count(); $i < 1; $i++)
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 3, 5)">Add</td>
                                                        @endfor

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="col-md-12">
                                    <h6>Finals Grade</h6>

                                    @php 
                                        $weight = array(
                                            'performance_task' => 50,
                                            'recitation' => 15,
                                            'quiz' => 25,
                                            'attendance' => 10,
                                        );

                                        $finalsScorePT = $totalScore->get('finals')->where('type', 'performance_task')->sum('score');
                                        $finalsOverScorePT = $totalScore->get('finals')->where('type', 'performance_task')->sum('over_score');
                                        $finalsScoreQuiz = $totalScore->get('finals')->where('type', 'quiz')->sum('score');
                                        $finalsOverScoreQuiz = $totalScore->get('finals')->where('type', 'quiz')->sum('over_score');
                                        $finalsScoreRecitation = $totalScore->get('finals')->where('type', 'recitation')->sum('score');
                                        $finalsOverScoreRecitation = $totalScore->get('finals')->where('type', 'recitation')->sum('over_score');

                                        
                                        $finalsClassStanding = 
                                            (((( $finalsOverScorePT == 0 ? 0:$finalsScorePT / $finalsOverScorePT) * 35 + 65) / 100) * $weight['performance_task'] ) +
                                            (((( $finalsOverScoreQuiz == 0 ? 0:$finalsScoreQuiz / $finalsOverScoreQuiz) * 35 + 65) / 100) * $weight['quiz']) +
                                            (((( $finalsOverScoreRecitation == 0 ? 0:$finalsScoreRecitation / $finalsOverScoreRecitation) * 35 + 65) / 100) * $weight['recitation']) +
                                            (((( $attendanceTotal == 0 ? 0:$attendancePresent / $attendanceTotal) * 35 + 65) / 100) * $weight['attendance'])
                                        ;

                                        $finalsExamScoreLec = $totalScore->get('finals')->where('type', 'lec')->sum('score');
                                        $finalsExamOverScoreLec = $totalScore->get('finals')->where('type', 'lec')->sum('over_score');
                                        $finalsExamScoreLab = $totalScore->get('finals')->where('type', 'lab')->sum('score');
                                        $finalsExamOverScoreLab = $totalScore->get('finals')->where('type', 'lab')->sum('over_score');

                                        $finalsExam = 
                                            (((( $finalsExamOverScoreLec == 0 ? 0:$finalsExamScoreLec / $finalsExamOverScoreLec) * 35 + 65) / 100) * 50) +
                                            (((( $finalsExamOverScoreLab == 0 ? 0:$finalsExamScoreLab / $finalsExamOverScoreLab) * 35 + 65) / 100) * 50)
                                        ;

                                    @endphp



                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>Grade</td>
                                                        <td><strong>{{ number_format(($finalsExam * 0.6) + ($finalsClassStanding * 0.4), 2) }}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Navigation Arrows -->
                <div class="row mt-4 text-center">
                    <div class="col">
                        <a href="{{ route('class-card.index', ['student_id' => $prevStudentId, 'subject_id' => $subjectId]) }}" class="btn btn-secondary">&lt;</a>
                        <a href="{{ route('class-card.index', ['student_id' => $nextStudentId, 'subject_id' => $subjectId]) }}" class="btn btn-secondary">&gt;</a>
                    </div>
                </div>
            </div>

        @endif
    </div>



    <!-- Performance Task Modal -->
    <div class="modal fade" id="performanceModal" tabindex="-1" aria-labelledby="performanceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="performanceModalLabel">Add Score</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="performanceForm" method="POST" action="{{ route('class-card.performance-task.store') }}">
                        @csrf
                        <input type="hidden" id="selected_exam_type" name="selected_exam_type">
                        <input type="hidden" id="class_card_id" name="class_card_id">
                        <input type="hidden" id="student_id_performance" name="student_id">
                        <input type="hidden" id="term" name="term"> <!-- Use a value for term: 1 for prelim, 2 for midterm -->
                        <input type="hidden" id="type_activity" name="type_activity"> <!-- Use a value for type of activity: 1 for performance task, 2 for quiz, 3 recitation -->
                        <div class="mb-3">
                            <label for="performanceScore" class="form-label">Score</label>
                            <input type="number" class="form-control" id="performanceScore" placeholder="Enter score" name="score" required>
                        </div>
                        <div class="mb-3">
                            <label for="over" class="form-label">Over Score</label>
                            <input type="number" class="form-control" id="over" placeholder="Over score" name="over_score" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Score</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editperformanceModal" tabindex="-1" aria-labelledby="editperformanceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editperformanceModalLabel">Edit Score</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editperformanceForm" method="POST">
                        @csrf
                        <!-- Spoof the PUT method -->
                        @method('PUT')
                        <input type="hidden" id="selected_exam_type" name="selected_exam_type">
                        <div class="mb-3">
                            <label for="edit_performanceScore" class="form-label">Score</label>
                            <input type="number" class="form-control" id="edit_performanceScore" placeholder="Enter score" name="score" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_performanceOverScore" class="form-label">Over Score</label>
                            <input type="number" class="form-control" id="edit_performanceOverScore" placeholder="Enter over score" name="over_score" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Score</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="removeTaskModal" tabindex="-1" aria-labelledby="removeTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeTaskModalLabel">Remove Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="taskTable">
                        <tbody id="taskTableBody">
                            <!-- Dynamic content will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openRemoveModal(taskType, term) {
            const taskTableBody = document.querySelector('#taskTableBody'); // Reference the tbody directly
            taskTableBody.innerHTML = ''; // Clear existing content

            let title;
            let tasks;

            // Fetch tasks based on taskType
            if(term === 'prelim'){
                if (taskType === 'performance_task') {
                    title = 'Remove Performance Task';
                    tasks = @json($scores->get('prelim')->where('type', 'performance_task'));
                } else if (taskType === 'quiz') {
                    title = 'Remove Quiz';
                    tasks = @json($scores->get('prelim')->where('type', 'quiz'));
                } else if (taskType === 'recitation') {
                    title = 'Remove Recitation';
                    tasks = @json($scores->get('prelim')->where('type', 'recitation'));
                }
            }else if(term === 'midterm'){
                if (taskType === 'performance_task') {
                    title = 'Remove Performance Task';
                    tasks = @json($scores->get('midterm')->where('type', 'performance_task'));
                } else if (taskType === 'quiz') {
                    title = 'Remove Quiz';
                    tasks = @json($scores->get('midterm')->where('type', 'quiz'));
                } else if (taskType === 'recitation') {
                    title = 'Remove Recitation';
                    tasks = @json($scores->get('midterm')->where('type', 'recitation'));
                }
            }else if(term === 'finals'){
                if (taskType === 'performance_task') {
                    title = 'Remove Performance Task';
                    tasks = @json($scores->get('finals')->where('type', 'performance_task'));
                } else if (taskType === 'quiz') {
                    title = 'Remove Quiz';
                    tasks = @json($scores->get('finals')->where('type', 'quiz'));
                } else if (taskType === 'recitation') {
                    title = 'Remove Recitation';
                    tasks = @json($scores->get('finals')->where('type', 'recitation'));
                }
            }
            

            document.getElementById('removeTaskModalLabel').textContent = title;

            // Populate the table rows dynamically using a normal for loop
            for (let key in tasks) {
                if (tasks.hasOwnProperty(key)) {
                    const task = tasks[key];
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="text-center">${taskType.charAt(0).toUpperCase() + taskType.slice(1)} ${task.item}</td>
                        <td class="text-center">
                            <button class="btn btn-danger" onclick="deletePerformance(${taskType === 'performance_task' ? 1 : (taskType === 'quiz' ? 2 : 3)}, ${term === 'prelim' ? '1' : (term === 'midterm' ? '2' : '3')}, ${task.item}, '{{ csrf_token() }}')">Remove</button>
                        </td>
                    `;
                    taskTableBody.appendChild(row);
                }
            }

            // Show the modal
            $('#removeTaskModal').modal('show');
        }
    </script>

    <script src="{{ asset('js/class-card.js') }}"></script>
    <script>
        // Function to save the selected exam type in local storage
        function saveExamType() {
            const examType = document.getElementById('exam_type').value;
            localStorage.setItem('selectedExamType', examType); // Save selected exam type to local storage
            showExamTables(); // Call function to show the corresponding table
        }

        // Function to show the tables based on the selected exam type
        function showExamTables() {
            const examType = document.getElementById('exam_type').value;
            const examTables = document.querySelectorAll('.exam-tables');

            examTables.forEach(table => {
                table.style.display = 'none'; // Hide all tables
            });

            if (examType === '1') {
                document.getElementById('prelim-tables').style.display = 'block'; // Show prelim tables
            } else if (examType === '2') {
                document.getElementById('midterm-tables').style.display = 'block'; // Show midterm tables
            } else if (examType === '3') {
                document.getElementById('finals-tables').style.display = 'block'; // Show finals tables
            }
        }

        // On page load, check local storage for the selected exam type
        window.onload = function() {
            const savedExamType = localStorage.getItem('selectedExamType');
            if (savedExamType) {
                document.getElementById('exam_type').value = savedExamType; // Set the dropdown to the saved value
                showExamTables(); // Show the corresponding table
            }
        };
    </script>
@endsection
