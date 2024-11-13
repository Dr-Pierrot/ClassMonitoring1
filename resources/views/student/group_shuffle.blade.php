@extends('layouts.app')

@section('title', 'Group Shuffling')

@section('content')
<div class="container mt-5">
    <h2 style="color: #E3A833;">Group Shuffling</h2>

    <!-- Display Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Group Shuffling Section -->
    <div class="card mb-4" style="background-color: #ffffff;">
        <div class="card-header" style="background-color: #E3A833; color: white;">
            <h4>Group Shuffling</h4>
        </div>
        <div class="card-body" style="background-color: #F6F9FF;">
            <form action="{{ route('students.group.shuffle') }}" method="POST">
                @csrf <!-- This is necessary for CSRF protection -->
                <div class="form-group">
                    <label for="subject">Select Subject:</label>
                    <select class="form-control" id="subject" name="subject_id">
                        <option value="">All Subjects</option>
                        @if(isset($subjects))
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ isset($subjectId) && $subjectId == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="section">Select Section:</label>
                    <select class="form-control" id="section" name="section_id">
                        <option value="">All Sections</option>
                        @if(isset($sections))
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ isset($sectionId) && $sectionId == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="students_per_group">Number of Students Per Group:</label>
                    <input type="number" class="form-control" id="students_per_group" name="students_per_group" min="1" required>
                </div>
                <br>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="background-color:#E3A833;border-color: #E3A833;">Shuffle into Groups</button>
                </div>
            </form>

            <h5 class="mt-4" style="color: #E3A833;">Student Groups:</h5>

            <!-- Table to display groups -->
            @if(isset($groups) && count($groups) > 0)
                @foreach($groups as $index => $group)
                    <h6 style="color: #E3A833;">Group {{ $index + 1 }}</h6>
                    <table class="table table-bordered mt-3" style="background-color: #ffffff;">
                        <thead>
                            <tr>
                                <th>Count</th>
                                <th>Student Number</th>
                                <th>Student Name</th>
                                <th>Section</th>
                                <th>Subject</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($group) > 0)
                                @foreach($group as $student)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $student->student->student_number }}</td>
                                        <td>{{ $student->student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</td>
                                        <td>{{ $student->section->name }}</td>
                                        <td>{{ $student->subject->name }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">No students found in this group.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                @endforeach
            @else
                <p>No groups created yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
