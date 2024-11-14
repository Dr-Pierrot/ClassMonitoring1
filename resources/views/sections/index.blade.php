@extends('layouts.app')

@section('title', 'Sections')

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
                        <h3 class="card-title text-center" style="color: #012970;">Sections</h3>
                    </div>
                    <div class="card-body">
                        <br>
                        <!-- Button to trigger create modal -->
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createSectionModal">
                            Create Section
                        </button>

                        <!-- Sections Table with Show Students link -->
                        <table class="table table-bordered" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="col-1">ID</th>
                                    <th class="col-3">Year Level</th>
                                    <th class="col-3">Section</th>
                                    <th class="col-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $years = array(1 => 'First Year', 2 => 'Second Year', 3 => 'Third Year', 4 => 'Fourth Year'); ?>
                                @foreach($sections as $section)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $years[$section->name] }}</td>
                                        <td>BSIT - {{ $section->name }}{{ $section->description }}</td>
                                        <td>
                                            <!-- Link to show students in this section -->
                                            <a href="{{ route('sections.students', $section->id) }}" class="btn btn-success btn-sm">
                                                Show Students
                                            </a>
                                            <!-- Other actions like Edit/Delete can also go here -->
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showEditModal({{ $section }})">
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal({{ $section->id }})">
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

    <!-- Create Section Modal -->
    <div class="modal fade" id="createSectionModal" tabindex="-1" aria-labelledby="createSectionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createSectionModalLabel">Create Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form for creating section -->
                    <form method="POST" action="{{ route('sections.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Year Level</label>
                            <select class="form-control" id="name" name="name" required>
                                <option value="">Select Year Level</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Section</label>
                            <select class="form-control" id="description" name="description" required>
                                <option value="">Select Section</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                                <option value="F">F</option>
                                <option value="G">G</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Section Modal -->
    <div class="modal fade" id="editSectionModal" tabindex="-1" aria-labelledby="editSectionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSectionModalLabel">Edit Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form for editing section -->
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Year Level</label>
                            <select class="form-control" id="edit_name" name="edit_name" required>
                                <option value="">Select Year Level</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Section</label>
                            <select class="form-control" id="edit_description" name="edit_description" required>
                                <option value="">Select Section</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                                <option value="F">F</option>
                                <option value="G">G</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Section Modal -->
    <div class="modal fade" id="deleteSectionModal" tabindex="-1" aria-labelledby="deleteSectionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteSectionModalLabel">Delete Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this section?
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/section-modal.js') }}"></script>
@endsection
