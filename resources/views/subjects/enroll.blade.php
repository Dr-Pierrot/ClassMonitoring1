@extends('layouts.app')

@section('title', 'Enroll Students')

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
                    <div class="card-header">
                        <h5 class="card-title">Enroll Students - {{ $subject->name }}</h5>
                    </div>
                    <div class="card-body">
                    <form id="enrollForm" action="{{ route('subjects.enrolls') }}" method="POST">
                        @csrf
                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                        <input type="hidden" name="enroll_type" id="enroll_type" value="single"> <!-- New hidden field -->
                        <div class="row mt-3">
                            <div class="col-4">
                                <select name="section_id" id="section" class="form-control" onchange="filterSection(this.value)">
                                    <option value="">Select Section</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <select name="student_id" id="student_id" class="form-control" onchange="setEnrollType(this.value)">
                                    <option value="">Select Student</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <!-- Button to trigger modal -->
                                <button type="submit" class="btn btn-primary mb-3">
                                    Enroll Student
                                </button>
                            </div>
                        </div>
                    </form>


                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Section</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($enrolls as $enroll)
                                            <tr>
                                                <td>{{ $enroll->student->first_name }} {{ $enroll->student->middle_name }} {{ $enroll->student->last_name }}</td>
                                                <td>{{ $enroll->student->section->name }}</td>
                                                <td>
                                                <form action="{{ route('subjects.enrolls.destroy', $enroll->id) }}" method="POST" onsubmit="return confirmDelete();">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
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
        </div>
    </div>
    <script>
        function filterSection(sectionID){
            const studentSelect = document.getElementById("student_id");
            studentSelect.innerHTML = '<option value="">Select Student</option>';
            @foreach ($students as $student)
                if(sectionID == "{{ $student->section_id }}") {
                    studentSelect.innerHTML += '<option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</option>';
                }else if(sectionID == "") {
                    studentSelect.innerHTML += '<option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</option>';
                }
            @endforeach
            setEnrollType('');
        }
        function setEnrollType(studentID) {
            const enrollTypeField = document.getElementById('enroll_type');
            enrollTypeField.value = studentID ? 'single' : 'section'; // Set to 'single' if a student is selected, otherwise 'section'
        }
        function confirmDelete() {
            return confirm("Are you sure you want to delete this enrollment?");
        }
    </script>
    

@endsection
