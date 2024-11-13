@extends('layouts.app')

@section('title', 'Students')

@section('content')
    <style>
        /* Background and styling */
        body {
            background-color: #F6F9FF;
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 15px;
        }

        /* Table styles */
        .table {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: #E3A833;
            color: #ffffff;
        }

        /* Button styles */
        .btn-primary {
            background-color: #E3A833;
            border-color: #E3A833;
            color: #ffffff;
        }

        .btn-secondary {
            background-color: #A8A8A8;
            border-color: #A8A8A8;
            color: #ffffff;
        }

        .btn-danger {
            background-color: #ff4d4d;
            border-color: #ff4d4d;
            color: #ffffff;
        }

        /* Landscape mode */
        @media screen and (orientation: landscape) and (max-width: 767px) {
            .container {
                max-width: 100%;
            }
        }

        /* Prompt message */
        #rotatePrompt {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.7);
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            z-index: 9999;
        }
    </style>

<div id="rotatePrompt">
        Please rotate your device to landscape mode for a better experience.
    </div>

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

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Students</h5>

                        <!-- Dropdown for exporting -->
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createStudentModal">
                                    Add Student
                                </button>

                                <button type="button" class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">
                                    Upload Excel
                                </button>

                                <a href="{{ route('students.exportStudentSheet') }}" class="btn btn-primary mb-3">
                                    Generate Sheet
                                </a>
                            </div>
                            <div class="col-6 text-end">
                                <a href="{{ route('students.exportStudents') }}" class="btn btn-success">Export Students</a>
                            </div>
                        </div>

                        <!-- Dropdowns for filtering -->
                        <form method="GET" action="{{ route('students.index') }}">
                            <div class="row mb-3">
                                <div class="col-md-5">
                                    <select class="form-control" name="section_id">
                                        <option value="">All Sections</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                                {{ $section->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="submit" class="btn btn-primary form-control">Filter</button>
                                </div>
                            </div>
                        </form>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Student Number</th>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Date of Birth</th>
                                    <th>Student Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    <tr>
                                        <td>{{ $student->student_number }}</td>
                                        <td>{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</td>
                                        <td>{{ $student->gender }}</td>
                                        <td>{{ $student->date_of_birth }}</td>
                                        <td>{{ $student->student_type }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showEditModal({{ $student }})">
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal({{ $student->id }})">
                                                Delete
                                            </button>
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

    <!-- Create Student Modal -->
    <div class="modal fade" id="createStudentModal" tabindex="-1" aria-labelledby="createStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createStudentModalLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form for creating student -->
                    <form method="POST" action="{{ route('students.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="student_number" class="form-label">Student Number</label>
                            <input type="text" class="form-control" id="student_number" name="student_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required max="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="course" class="form-label">Course</label>
                            <input type="text" class="form-control" id="course" name="course" value="BSIT" readonly required>
                        </div>


                        <!-- Section Dropdown -->
                        <div class="mb-3">
                            <label for="section_id" class="form-label">Section</label>
                            <select class="form-select" id="section_id" name="section_id" required>
                                <option value="">Select Section</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Student Type Dropdown -->
                        <div class="mb-3">
                            <label for="student_type" class="form-label">Student Type</label>
                            <select class="form-select" id="student_type" name="student_type" required>
                                <option value="">Select Student Type</option>
                                <option value="regular">Regular</option>
                                <option value="irregular">Irregular</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-2" aria-labelledby="editStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form for editing student -->
                    <form id="editForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <!-- Fields similar to create modal, pre-filled -->
                        <div class="mb-3">
                            <label for="edit_student_number" class="form-label">Student Number</label>
                            <input type="text" class="form-control" id="edit_student_number" name="student_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="edit_middle_name" name="middle_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="edit_date_of_birth" name="date_of_birth" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_gender" class="form-label">Gender</label>
                            <input type="text" class="form-control" id="edit_gender" name="gender" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_course" class="form-label">Course</label>
                            <input type="text" class="form-control" id="edit_course" name="course" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_section_id" class="form-label">Section</label>
                            <select class="form-select" id="edit_section_id" name="section_id" required>
                                <option value="">Select Section</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_student_type" class="form-label">Student Type</label>
                            <select class="form-select" id="edit_student_type" name="student_type" required>
                                <option value="">Select Student Type</option>
                                <option value="regular">Regular</option>
                                <option value="irregular">Irregular</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Student Modal -->
    <div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteStudentModalLabel">Delete Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this student?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Excel Modal -->
    <div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadExcelModalLabel">Upload Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('students.uploadExcel') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Excel File</label>
                            <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx" required>
                        </div>
                        <div class="mb-3">
                            <label for="section_id" class="form-label">Section</label>
                            <select class="form-select" id="section_id" name="section_id" required>
                                <option value="">Select Section</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Function to check orientation and show prompt if in portrait mode
        function checkOrientation() {
            const isPortrait = window.innerHeight > window.innerWidth;
            const rotatePrompt = document.getElementById("rotatePrompt");
            rotatePrompt.style.display = isPortrait ? "flex" : "none";
        }

        // Run check on load and on resize
        window.addEventListener("load", checkOrientation);
        window.addEventListener("resize", checkOrientation);
    </script>
@endsection
<script src="{{ asset('js/students-modal.js') }}"></script>
