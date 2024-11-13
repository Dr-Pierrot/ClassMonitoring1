@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">
    <!-- Welcome Message -->
    <h2 class="mb-4 text-center">Welcome, {{ Auth::user()->name }}!</h2>

    <!-- Row for the cards -->
    <div class="row text-center">
        <!-- Total Students Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow border-custom rounded-lg">
                <div class="card-body">
                    <h3 class="card-title text-warning custom-display">{{ $studentsCount }}</h3>
                    <p class="card-text">Total Students</p>
                </div>
            </div>
        </div>

        <!-- Total Subjects Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow border-custom rounded-lg">
                <div class="card-body">
                    <h3 class="card-title text-primary custom-display">{{ $subjectsCount }}</h3>
                    <p class="card-text">Total Subjects</p>
                </div>
            </div>
        </div>

        <!-- Total Sections Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow border-custom rounded-lg">
                <div class="card-body">
                    <h3 class="card-title text-success custom-display">{{ $sectionsCount }}</h3>
                    <p class="card-text">Total Sections</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS to maintain styling without forced centering -->
<style>
    /* Custom Hover Background and Text Color Change */
    .side-link a:hover {
        background-color: #8c8b89;
        color: white;
        border-radius: 5px;
    }

    .side-link a:hover span,
    .side-link a:hover i {
        color: #FFF;
    }

    .custom-display {
        font-size: 4rem;
        font-weight: bold;
    }

    .border-custom {
        border: 2px solid #E3A833;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
    }

    /* Hover effect for the borders */
    .border-custom:hover {
        border-color: #E3A833;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
    }
</style>
@endsection
