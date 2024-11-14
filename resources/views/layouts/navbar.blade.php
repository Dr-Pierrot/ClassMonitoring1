<style>
    /* Custom styling for the navbar */
    .logo span {
        color: #E3A833; /* Set the color for the title */
        font-weight: bold; /* Make the title bold */
    }

    /* Navbar Styles */
    .header {
        background-color: #ffffff; /* White background for the header */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Subtle shadow for the navbar */
        padding: 0.5rem 1rem; /* Padding around the navbar */
    }

    .header .logo img {
        max-height: 40px; /* Set the logo size */
    }

    .header-nav {
        font-size: 16px; /* Adjust font size for navbar items */
    }

    /* Navbar links */
    .header-nav .nav-link {
        color: #343a40; /* Dark text color for better readability */
        padding: 0.75rem 1.25rem; /* Padding around each navbar item */
        border-radius: 5px; /* Rounded corners for the links */
        transition: background-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for hover effects */
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2); /* Adding shadow to the text for better readability */
    }

    .header-nav .nav-link:hover {

        color: #E3A833; /* White text color on hover */
    }

    .header-nav .nav-item.dropdown .dropdown-menu {
        border-radius: 5px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); /* Subtle dropdown shadow */
        border: none;
    }

    .header-nav .dropdown-item {
        color: #343a40; /* Dark text for dropdown items */
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2); /* Adding shadow to the text for readability */
    }

    .header-nav .dropdown-item:hover {
        background-color: #f8f9fa; /* Light background on hover */
        color: #E3A833; /* Gold text color on hover */
    }

    .header-nav .nav-profile {
        color: #343a40; /* Profile dropdown text color */
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2); /* Adding shadow to the profile text */
    }

    .header-nav .dropdown-header {
        background-color: #f8f9fa; /* Light background for the header inside the dropdown */
        color: #343a40; /* Dark text for the profile name */
    }

    /* Style for the Burger Menu */
    .toggle-sidebar-btn {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 30px;
        height: 25px;
        cursor: pointer;
        padding: 5px;
        transition: all 0.3s ease;
        background: transparent;
        border: none;
        z-index: 1001; /* Ensure it's on top of other elements */
    }

    .toggle-sidebar-btn span {
        display: block;
        height: 4px;
        width: 100%;
        background-color: #343a40; /* Dark color for the burger lines */
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    /* When the burger menu is active */
    .toggle-sidebar-btn.active span:nth-child(1) {
        transform: rotate(45deg) translateY(7px); /* Rotate top line to make X */
    }

    .toggle-sidebar-btn.active span:nth-child(2) {
        opacity: 0; /* Hide the middle line */
    }

    .toggle-sidebar-btn.active span:nth-child(3) {
        transform: rotate(-45deg) translateY(-7px); /* Rotate bottom line to make X */
    }

    /* Hover and Active states for the burger menu */
    .toggle-sidebar-btn:hover span {
        background-color: #E3A833; /* Gold color for the lines on hover */
    }

    .toggle-sidebar-btn:hover {
        transform: scale(1.2); /* Slightly increase size on hover */
    }

    /* When burger menu is active (open sidebar) */
    .toggle-sidebar-btn.active {
        transform: rotate(180deg); /* Smooth rotation when active */
    }

    /* Optional: Add a glow effect for interactivity */
</style>

<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <!-- Logo Section -->
        <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center justify-content-center">
            <img src="{{ asset('assets/img/logo2.png') }}" alt="Class Monitoring Logo" class="text-center" width="100">
            <!-- <span class="d-none d-lg-inline text-warning ms-2">Class Monitoring</span> <!-- Logo Text -->
        </a>
        <i class="bi bi-list toggle-sidebar-btn" onclick="toggleMenu()"> <!-- Updated burger menu -->
            <!--<span></span>-->
            <span></span>
            <span></span>
        </i>
    </div>

    <!-- Navigation Section -->
    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center mb-0">
            <!-- Profile Dropdown -->
            <li class="nav-item dropdown pe-3">
    <!-- Profile Dropdown Toggle -->
    <a id="profileDropdownToggle" class="nav-link nav-profile d-flex align-items-center pe-0" href="#" aria-expanded="false">
        <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->name }}</span>
    </a>

    <!-- Profile Dropdown Menu -->
    <ul id="profileDropdown" class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <!-- Profile Info Section -->
        <li class="dropdown-header">
            <h6>{{ Auth::user()->name }}</h6>
            <span>{{ Auth::user()->email }}</span>
        </li>
        <li><hr class="dropdown-divider"></li>

        <!-- Change Password Option -->
        <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('password.change') }}">
                <i class="bi bi-key"></i>
                <span>Change Password</span>
            </a>
        </li>

        <!-- Logout Option -->
        <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</li>

        </ul>
    </nav>
</header>

<!-- Hidden Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the dropdown toggle link
        var profileDropdownToggle = document.getElementById('profileDropdownToggle');
        
        // Get the dropdown menu
        var profileDropdown = document.getElementById('profileDropdown');
        
        // Create a new instance of Bootstrap's Dropdown
        var dropdown = new bootstrap.Dropdown(profileDropdownToggle);

        // Add event listener to toggle the dropdown when the link is clicked
        profileDropdownToggle.addEventListener('click', function(event) {
            event.preventDefault();  // Prevent the default action (href)
            
            // Toggle the dropdown menu visibility
            dropdown.toggle();
        });

        // Optional: Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!profileDropdown.contains(event.target) && !profileDropdownToggle.contains(event.target)) {
                // If clicked outside the dropdown, close it
                dropdown.hide();
            }
        });
    });
</script>



<script>
    // Function to toggle the burger menu's active state
    function toggleMenu() {
        const burger = document.querySelector('.toggle-sidebar-btn');
        burger.classList.toggle('active');
    }
</script>
