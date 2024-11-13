
@extends('layouts.app')

@section('title', "Students in $section->name")

@section('content')
    <div class="container">
        <h3 class="text-center mt-4">Students in {{ $section->name }} - {{ $section->description }}</h3>

        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Student Number</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
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

        <a href="{{ route('sections.index') }}" class="btn btn-secondary mt-3">Back to Sections</a>
    </div>
@endsection
