@extends('layouts.app')

@section('title', 'Choose Subjects')

@section('content')
    <div class="container">
        <!-- Flash Message -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card" style="background-color: #fff; border: 1px solid #cddfff;">
                    <div class="card-body">
                        <h5 class="card-title">Choose Subjects</h5>
                        
                        <!-- Search Bar with Button -->
                        <div class="input-group mb-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search subjects by name...">
                            <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>

                        <!-- Import Button -->
                        <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#importSubjectModal">
                            Import Subject
                        </button>

                        <!-- Form for selecting multiple subjects -->
                        <form action="{{ route('subjects.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Table for displaying subjects with checkboxes -->
                            <table class="table table-bordered" style="width: 100%;" id="subjectsTable">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>ID</th>
                                        <th>Course Code</th>
                                        <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjects as $subject)
                                        <tr class="subject-row">
                                            <td>
                                                <input type="checkbox" name="subjects[]" value="{{ $subject->id }}">
                                            </td>
                                            <td>{{ $subject->id }}</td>
                                            <td>{{ $subject->course_code }}</td>
                                            <td class="subject-name">{{ $subject->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Submit Button for the selected subjects -->
                            <button type="submit" class="btn btn-primary">Import Selected Subjects</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Subject Modal -->
    <div class="modal fade" id="importSubjectModal" tabindex="-1" aria-labelledby="importSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importSubjectModalLabel">Import Subjects</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form to import subjects -->
                    <form method="POST" action="{{ route('subjects.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="subject_file" class="form-label">Choose file</label>
                            <input type="file" class="form-control" id="subject_file" name="subject_file" accept=".csv,.xls,.xlsx" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Search button functionality
    const searchButton = document.getElementById('searchButton');
    const searchInput = document.getElementById('searchInput');

    searchButton.addEventListener('click', function () {
        const searchTerm = searchInput.value.toLowerCase();
        filterTable(searchTerm);
    });

    // Function to filter table rows based on search term
    function filterTable(searchTerm) {
        const rows = Array.from(document.querySelectorAll('#subjectsTable tbody tr'));

        rows.forEach(row => {
            const subjectName = row.querySelector('.subject-name').textContent.toLowerCase();
            const isMatch = subjectName.includes(searchTerm);

            // Toggle row visibility
            row.style.display = isMatch ? '' : 'none';

            // Optionally highlight matching text (clear first)
            row.querySelector('.subject-name').innerHTML = row.querySelector('.subject-name').textContent;
            if (isMatch && searchTerm) {
                const highlighted = row.querySelector('.subject-name').textContent.replace(
                    new RegExp(`(${searchTerm})`, 'gi'),
                    '<span class="bg-warning">$1</span>'
                );
                row.querySelector('.subject-name').innerHTML = highlighted;
            }
        });
    }
</script>
@endsection
