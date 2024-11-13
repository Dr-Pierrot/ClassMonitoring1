<style>
/* Custom Hover Background and Text Color Change */
.side-link a:hover {
    background-color: #8c8b89; /* Background color on hover */
    color: white; /* Text color change on hover */
    border-radius: 5px; /* Rounded corners */
}

/* Optional: Specific hover effect for text only */
.side-link a:hover span {
    color: #FFF; /* Change text color to your desired color on hover */
}
.side-link a:hover i {
    color: #FFF; /* Change icon color on hover */
}

</style>


<aside id="sidebar" class="sidebar bg-white p-3 border-end" style="border-color: #E3A833;">
    <!-- Dashboard Nav -->
    <div class="side-link mb-3">
        <a class="nav-link d-flex align-items-center text-dark p-2 rounded hover-bg" href="{{ route('dashboard') }}">
            <i class="bi bi-house me-2 fs-5"></i><span>Dashboard</span>
        </a>
    </div>

    <!-- Sections, Subjects, and Manage Students Nav (only for teacher) -->
    @if(auth()->check() && auth()->user()->user_type === 'teacher')
        <div class="side-link mb-3">
            <a class="nav-link d-flex align-items-center text-dark p-2 rounded hover-bg" href="{{ route('subjects.index') }}">
                <i class="bi bi-card-text me-2 fs-5"></i><span>Subjects</span>
            </a>
        </div>
        <div class="side-link mb-3">
            <a class="nav-link d-flex align-items-center text-dark p-2 rounded hover-bg" href="{{ route('sections.index') }}">
                <i class="bi bi-list-ol me-2 fs-5"></i><span>Sections</span>
            </a>
        </div>
        <div class="side-link mb-3">
            <a class="nav-link d-flex align-items-center text-dark p-2 rounded hover-bg" href="{{ route('students.index') }}">
                <i class="bi bi-people me-2 fs-5"></i><span>Manage Students</span>
            </a>
        </div>
        <div class="side-link mb-3">
            <a class="nav-link d-flex align-items-center text-dark p-2 rounded hover-bg" href="{{ route('students.group.shuffle') }}">
                <i class="bi bi-shuffle me-2 fs-5"></i><span>Shuffle Group</span>
            </a>
        </div>
    @endif

    <!-- User Management (only for admin) -->
    @if(auth()->check() && auth()->user()->user_type === 'admin')
        <div class="side-link mb-3">
            <a class="nav-link d-flex align-items-center text-dark p-2 rounded hover-bg" href="{{ route('users.index') }}">
                <i class="bi bi-people-fill me-2 fs-5"></i><span>User Management</span>
            </a>
        </div>
        <div class="side-link mb-3">
            <a class="nav-link d-flex align-items-center text-dark p-2 rounded hover-bg" href="{{ route('register') }}">
                <i class="bi bi-person-plus-fill me-2 fs-5"></i><span>Register New User</span>
            </a>
        </div>
    @endif
</aside>
