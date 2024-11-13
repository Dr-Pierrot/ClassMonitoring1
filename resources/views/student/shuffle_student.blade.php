@extends('layouts.app')

@section('title', 'Student Shuffling')

@section('content')
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
    <h2 style="color: #E3A833;">Student Shuffling</h2>

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

    <!-- Recitation Shuffling Section -->
    <div class="card mb-4" style="background-color: #ffffff;">
        <div class="card-header" style="background-color: #E3A833; color: white;">
            <h4>Recitation Shuffling</h4>
        </div>
        <div class="card-body" style="background-color: #F6F9FF;">
            <form action="{{ route('students.shuffle') }}" method="POST">
                @csrf <!-- This is necessary for CSRF protection -->
                <input type="hidden" name="subject_id" id="subject_id" value="{{ $subjectId }}">
                <div class="form-group">
                    <label for="section">Select Section:</label>
                    <select class="form-control" id="section" name="section_id">
                        <option value="">Select a Section</option>
                        @if(isset($sections))
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <br>
                <button type="submit" class="btn btn-primary" style="background-color:#E3A833;border-color: #E3A833;">Shuffle Recitation</button>
            </form>
            <h5 class="mt-4">Shuffled Students:</h5>
            <form action="{{ route('recitation.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <label for="term" class="form-label">Term</label>
                        <select class="form-select" id="term" name="term" required>
                            <option value="1" {{ isset($term) && $term == 1 ? 'selected' : '' }}>Prelim</option>
                            <option value="2" {{ isset($term) && $term == 2 ? 'selected' : '' }}>Midterm</option>
                            <option value="3" {{ isset($term) && $term == 3 ? 'selected' : '' }}>Finals</option>
                        </select>
                    </div>
                </div>
                <table class="table table-bordered mt-3" style="background-color: #ffffff;">
                    <thead>
                        <tr>
                            <th>Count</th>
                            <th>Student Name</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Score</th>
                            <th>OverScore</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($shuffledStudents) && $shuffledStudents->isNotEmpty())
                            @foreach($shuffledStudents as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->student->last_name }}, {{ $student->student->first_name }} {{ $student->student->middle_name }}</td>
                                    <td>{{ $student->section->name }}</td>
                                    <td>{{ $student->subject->name }}</td>
                                    <td class="text-center">
                                        <input type="number" name="scores[{{ $student->student->id }}][score]" id="score_{{ $student->student->id }}" class="form-control" placeholder="Enter score" min="0" required>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" name="scores[{{ $student->student->id }}][over_score]" id="over_score_{{ $student->student->id }}" class="form-control" placeholder="Enter over score" min="0" required>
                                    </td>
                                    <!-- Hidden input for class_card_id -->
                                    <input type="hidden" name="scores[{{ $student->student->id }}][class_card_id]" value="{{ $student->id }}">
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">No students found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary mt-3">Save Recitation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Add event listeners for each over score input
    document.querySelectorAll('input[id^="over_score_"]').forEach(input => {
        input.addEventListener('input', function(event) {
            const newValue = event.target.value; // Get the new value from the changed input

            // Update all over_score inputs with the new value
            document.querySelectorAll('input[id^="over_score_"]').forEach(overScoreInput => {
                overScoreInput.value = newValue; // Set each input to the new value
            });
        });
    });
</script>
@endsection
