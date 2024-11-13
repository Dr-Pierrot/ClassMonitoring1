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
                    <div class="card-body">
                        <h5 class="card-title text-center" style="color: #012970;">Sections</h5>
                        <!-- Button to trigger create modal -->
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createSectionModal">
                            Create Section
                        </button>

                        <!-- Sections Table with Show Students link -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Year Level</th>
                                    <th>Section</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sections as $section)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $section->name }}</td>
                                        <td>{{ $section->description }}</td>
                                        <td>
                                            <!-- Link to show students in this section -->
                                            <a href="{{ route('sections.students', $section->id) }}" class="btn btn-success btn-sm">
                                                Show Students
                                            </a>
                                            <!-- Other actions like Edit/Delete can also go here -->
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showEditModal({{ $section->id }})">
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
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Section</label>
                            <select class="form-control" id="description" name="description" required>
                                <option value="">Select Section</option>
                                <option value="BSIT - A">A</option>
                                <option value="BSIT - B">B</option>
                                <option value="BSIT - C">C</option>
                                <option value="BSIT - D">D</option>
                                <option value="BSIT - E">E</option>
                                <option value="BSIT - F">F</option>
                                <option value="BSIT - G">G</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/section-modal.js') }}"></script>
@endsection
