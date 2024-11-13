<table>
    <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Section</th>
            <th>Subject</th>
            <th>Course Code</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $student)
        <tr>
            <td>{{ $student->first_name }}</td>
            <td>{{ $student->last_name }}</td>
            <td>{{ optional($student->section)->name }}</td>
            <td>{{ optional($student->section->subject)->name }}</td>
            <td>{{ optional($student->section->subject)->course_code }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
