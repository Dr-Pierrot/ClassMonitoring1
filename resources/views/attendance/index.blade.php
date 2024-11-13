@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
<style>
    .square-table td {
        width: 100px;
        height: 50px;
        text-align: center;
        vertical-align: middle; /* Aligns content vertically */
    }
    .table .hover:hover {
        background-color: wheat;
        cursor: pointer;
    }
</style>
<div class="container mt-5">
        <form action="{{ route('attendance.index') }}" method="GET">
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
                        <div class="col-md-6">
                            <p><strong>Section:</strong> {{ $student->section->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Attendance Type Selection Dropdown -->
<div class="row mb-4">
    <!-- Attendance Type Selection -->
    <div class="col-md-4">
        <label for="attendanceType">Select Attendance Type:</label>
        <select id="attendanceType" class="form-control">
            <option value="both">Both</option>
            <option value="lecture">Lecture Only</option>
            <option value="laboratory">Laboratory Only</option>
        </select>
    </div>

        <!-- Lecture Attendance Table -->
        <div id="lectureAttendance" class="attendance-table">
            <h4>Lecture Attendance</h4>
            <div style="overflow-x:auto;">
                <table class="table table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th>Days</th>
                            @for ($i = 1; $i <= 18; $i++)
                                <th>{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6] as $day => $dayNum)
                            <tr class="square-table">
                                <td>{{ $day }}</td>
                                @for ($i = 1; $i <= 18; $i++)
                                    @php
                                        // Check for existing attendance record for this day and date
                                        $attendance = $attendanceRecords
                                            ->where('day', $dayNum)
                                            ->where('type', 1)
                                            ->where('attendance_date', $i)
                                            ->first();
                                    @endphp
                                    <td id="lec-{{ $day }}-{{ $i }}" 
                                        class="hover"
                                        data-day="{{ $dayNum }}" 
                                        data-type="1" 
                                        data-attendance-date="{{ $i }}" 
                                        ondblclick="deleteAttendance(1, {{ $dayNum }}, {{ $i }})"
                                        onclick="checkAttendance(1, {{ $dayNum }}, {{ $i }}, {{ $attendance ? ($attendance->status == 1 ? 2 : 1) : 1}})"
                                        style="background-color: {{$attendance ? ($attendance->status == 1 ? 'Blue' : 'Red') : ''}};">
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Laboratory Attendance Table -->
        <div id="laboratoryAttendance" class="attendance-table" style="display: none;">
            <h4>Laboratory Attendance</h4>
            <div style="overflow-x:auto;">
                <table class="table table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th>Days</th>
                            @for ($i = 1; $i <= 18; $i++)
                                <th>{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6] as $day1 => $dayNumLab)
                            <tr class="square-table">
                                <td>{{ $day1 }}</td>
                                @for ($j = 1; $j <= 18; $j++)
                                    @php
                                        // Check for existing attendance record for laboratory
                                        $attendance_lab = $labAttendanceRecords
                                            ->Where('day', $dayNumLab)
                                            ->Where('type', 2)
                                            ->firstWhere('attendance_date', $j);
                                    @endphp
                                    <td id="lab-{{ $day }}-{{ $j }}" 
                                        class="hover"
                                        data-day="{{ $dayNum }}" 
                                        data-type="2" 
                                        data-attendance-date="{{ $j }}" 
                                        ondblclick="deleteAttendance(2, {{ $dayNumLab }}, {{ $j }})"
                                        onclick="checkAttendance(2, {{ $dayNumLab }}, {{ $j }}, {{ $attendance_lab ? ($attendance_lab->status == 1 ? 2 : 1) : 1}})"
                                        style="background-color: {{$attendance_lab ? ($attendance_lab->status == 1 ? 'Blue' : 'Red') : ''}};">
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Navigation Arrows -->
    <div class="row mt-4 text-center">
        <div class="col">
            <a href="{{ route('attendance.index', ['student_id' => $prevStudentId, 'subject_id' => $subjectId]) }}" class="btn btn-secondary">&lt;</a>
            <a href="{{ route('attendance.index', ['student_id' => $nextStudentId, 'subject_id' => $subjectId]) }}" class="btn btn-secondary">&gt;</a>
        </div>
    </div>
</div>

<script>
    // JavaScript to handle the dropdown change event
    document.getElementById('attendanceType').addEventListener('change', function() {
        const selectedType = this.value;

        
        // Hide all attendance tables initially
        document.querySelectorAll('.attendance-table').forEach(table => {
            table.style.display = 'none';
        });
        
        // Show the selected attendance table(s)
        if (selectedType === 'lecture') {
            document.getElementById('lectureAttendance').style.display = 'block';
        } else if (selectedType === 'laboratory') {
            document.getElementById('laboratoryAttendance').style.display = 'block';
        } else {
            document.getElementById('lectureAttendance').style.display = 'block';
            document.getElementById('laboratoryAttendance').style.display = 'block';
        }
    });
    
    // Trigger change event on page load to set the default view
    document.getElementById('attendanceType').dispatchEvent(new Event('change'));
</script>
<script>
    function checkAttendance(type, day, attendanceDate, status) {
        const studentId = {{ $student->id }};
        const subjectId = {{ $subjectId }};
        const sectionId = {{ $student->section->id }};

        // Fetch request to store attendance
        fetch('{{ route('attendance.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    student_id: studentId,
                    subject_id: subjectId,
                    section_id: sectionId,
                    day: day,
                    type: type,
                    status: status,
                    attendance_date: attendanceDate // Match this with the back-end
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Handle success response
                if (data.success) {
                    const redirectUrl = '{{ route('attendance.index', ['student_id' => $student->id, 'subject_id' => $subjectId, 'type' => ' + type + ']) }}';

                    // JavaScript redirection
                    window.location.href = redirectUrl;
                } else {
                    alert(data.error); // Handle any errors returned from the server
                }
            })
            .catch(error => {
                console.error('Error:', error); // Handle error response
            });
    }


    function deleteAttendance(type, day, attendanceDate) {
        const studentId = {{ $student->id }};
        const subjectId = {{ $subjectId }};
        const sectionId = {{ $student->section->id }};

        if (confirm('Are you sure you want to delete this attendance?')) {
            fetch('{{ route('attendance.delete') }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    student_id: studentId,
                    subject_id: subjectId,
                    section_id: sectionId,
                    day: day,
                    type: type,
                    attendance_date: attendanceDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error);    // Show any errors from the server
                }
            })
            .catch(error => {
                console.error('Error:', error);  // Handle network or other errors
            });
        }
    }
</script>
<script>
    // Function to toggle the visibility of lecture and laboratory tables
    function toggleAttendanceTables(selectedType) {
        const lectureDiv = document.getElementById('lectureAttendance');
        const laboratoryDiv = document.getElementById('laboratoryAttendance');

        if (selectedType === 'both') {
            // Show both
            lectureDiv.style.display = 'block';
            laboratoryDiv.style.display = 'block';
        } else if (selectedType === 'lecture') {
            // Show lecture, hide laboratory
            lectureDiv.style.display = 'block';
            laboratoryDiv.style.display = 'none';
        } else if (selectedType === 'laboratory') {
            // Show laboratory, hide lecture
            lectureDiv.style.display = 'none';
            laboratoryDiv.style.display = 'block';
        }
    }

    // Event listener for the dropdown change
    document.getElementById('attendanceType').addEventListener('change', function() {
        const selectedValue = this.value;

        // Save the selected value to localStorage
        localStorage.setItem('attendanceType', selectedValue);

        // Toggle tables based on the selected value
        toggleAttendanceTables(selectedValue);
    });

    // On page load, set the tables according to the stored value
    window.addEventListener('DOMContentLoaded', function() {
        const savedType = localStorage.getItem('attendanceType') || 'both';
        document.getElementById('attendanceType').value = savedType;

        // Initialize the attendance tables based on the saved value
        toggleAttendanceTables(savedType);
    });
</script>

<script src="{{ asset('js/attendance.js') }}"></script>

@endsection

