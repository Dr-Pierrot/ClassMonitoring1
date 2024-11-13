@extends('layouts.app')

@section('title', 'Subjects')

@section('content')
    <style>
        body {
            background-color: #F6F9FF; /* Background color of the page */
        }

        .card {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .table {
            background-color: #ffffff; /* Table background */
        }

        .table th {
            background-color: #E3A833; /* Header background color */
            color: white; /* Header text color */
        }

        .table tbody tr:hover {
            background-color: #f0f0f0; /* Row hover effect */
        }

        .btn-primary {
            background-color: #E3A833; /* Primary button color */
            border-color: #E3A833; /* Border color for primary buttons */
        }

        .btn-danger {
            background-color: #ff4d4d; /* Danger button color */
            border-color: #ff4d4d; /* Border color for danger buttons */
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">

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

                <div class="card" style="background-color: #fff; border: 1px solid #cddfff;">
                    <div class="card-body">
                        <h5 class="card-title">Subjects</h5>
                        
                        <!-- Choose Subjects Button -->
                        <!-- Update the button in your previous page -->
                         <a href="{{ route('subjects.choose') }}" class="btn btn-primary mb-3">
                            Choose Subjects
                        </a>
                        <table class="table table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Course Code</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjects as $subject)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $subject->course_code }}</td>
                                        <td>{{ $subject->name }}</td>
                                        <td>{{ $subject->description }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showEditModal({{ json_encode($subject) }})">Edit</button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal({{ $subject->id }})">Delete</button>
                                            <button class="btn btn-sm btn-success dropdown-toggle" type="button" id="otherDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                Other
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="otherDropdown">
                                                <li><a class="dropdown-item" href="{{ route('subjects.showEnroll', ['subject_id' => $subject->id]) }}">Enroll Students</a></li>
                                                <li><a class="dropdown-item" href="{{ route('attendance.index', ['subject_id' => $subject->id]) }}">Check Attendance</a></li>
                                                <li><a class="dropdown-item" href="{{ route('students.shuffle', ['subject_id' => $subject->id]) }}">Manage Recitation</a></li>
                                                <li><a class="dropdown-item" href="{{ route('class-card.index', ['subject_id' => $subject->id]) }}">Record Grades</a></li>
                                            </ul>
                                            <button class="btn btn-sm btn-success dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                Export
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                                <li><a class="dropdown-item" href="{{ route('students.exportPrelim', ['subject_id' => $subject->id]) }}">Export Prelim</a></li>
                                                <li><a class="dropdown-item" href="{{ route('students.exportMidterm', ['subject_id' => $subject->id]) }}">Export Midterm</a></li>
                                                <li><a class="dropdown-item" href="{{ route('students.exportFinals', ['subject_id' => $subject->id]) }}">Export Finals</a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Include subject-modals.js (remove this if you no longer need it for modal functionality) -->
    <script src="{{ asset('js/subject-modals.js') }}"></script>

@endsection
