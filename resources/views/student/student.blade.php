
@extends('layouts.app')

@section('title', "Students in $section->name")

@section('content')
    <style>
        body {
            background-color: #F6F9FF;
        }

        .card {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .table {
            background-color: #ffffff; /* Card background */
        }

        .table th {
            background-color: #E3A833; /* Header background color */
            color: white; /* Text color for header */
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

        .students-list {
            margin-top: 20px;
            display: none;
        }
        .btn-success {
            background-color: #28a745; /* Green button color */
            border-color: #28a745; /* Green border color */
        }
    </style>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title text-center">Students in {{ $section->name }} - {{ $section->description }}</h3>        
            </div>
            <div class="card-body">
                <table class="table table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th class="col-2">Student Number</th>
                            <th class="col-2">First Name</th>
                            <th class="col-2">Last Name</th>
                            <th class="col-2">Gender</th>
                            <th class="col-2">Date of Birth</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $student->student_number }}</td>
                                <td>{{ $student->first_name }}</td>
                                <td>{{ $student->last_name }}</td>
                                <td>{{ $student->gender }}</td>
                                <td>{{ $student->date_of_birth }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No students found in this section.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('sections.index') }}" class="btn btn-secondary mt-3">Back to Sections</a>        
            </div>
        </div>
    </div>
@endsection
