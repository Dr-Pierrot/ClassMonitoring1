@extends('layouts.app')

@section('title', 'Choose Subjects')

@section('content')
    <div class="container">
        <!-- Success/Error Message -->
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
                        <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                            Add Subject
                        </button>

                        <!-- Form for selecting multiple subjects -->
                        <form action="{{ route('subjects.addSelected') }}" method="POST">
                            @csrf

                            <!-- Table for displaying subjects with checkboxes -->
                            <table class="table table-bordered" style="width: 100%;" id="subjectsTable">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>ID</th>
                                        <th>Course Code</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Action</th>
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
                                            <td>{{ $subject->description }}</td>
                                            <td>
                                            <!-- Button to Trigger Modal -->
<button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSubjectModal" data-id="{{ $subject->id }}">
    Delete
</button>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Submit Button for the selected subjects -->
                            <button type="submit" class="btn btn-primary">Add Selected Subjects</button>
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
    <!-- Import Subject Modal -->
    <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubjectModalLabel">Add Subjects</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form to import subjects -->
                    <form method="POST" action="{{ route('subjects.add') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="course_code" class="form-label">Subject Code</label>
                            <input type="text" class="form-control" id="course_code" name="course_code" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Subject Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Subject Description</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
  <!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteSubjectModal" tabindex="-1" aria-labelledby="deleteSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSubjectModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this subject? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <!-- Form for deleting the subject -->
                <form id="deleteSubjectForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Get the modal and form
    const deleteSubjectModal = document.getElementById('deleteSubjectModal');
    const deleteSubjectForm = document.getElementById('deleteSubjectForm');

    // Add event listener for when the modal is shown
    deleteSubjectModal.addEventListener('show.bs.modal', function (event) {
        // Get the button that triggered the modal
        const button = event.relatedTarget;

        // Get the subject ID from the data-id attribute
        const subjectId = button.getAttribute('data-id');
        
        // Set the form action dynamically based on the subject ID
        const formAction = `/subjects/destroy/${subjectId}`;
        deleteSubjectForm.action = formAction;
        
        console.log("Form action updated to:", formAction);  // For debugging
    });
});
</script>

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
