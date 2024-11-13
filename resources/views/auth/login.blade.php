<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <!-- Favicon and Manifest -->
    <link rel="icon" href="{{ asset('assets/img/logo2.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#007bff">

    <!-- Essential Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#6777ef"/>
    <link rel="apple-touch-icon" href="{{ asset('assets/img/logo2.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    
    <!-- Add Web App Capabilities -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Class Monitoring">
    <meta name="description" content="Login to Class Monitoring - College of Computer Studies">
    
</head>
<body>
    <div class="container custom-container">
        <div class="left-panel">
            <img class="main-logo" src="assets/img/logo2.png" alt="College Logo">
            <p>for the College of Computer Studies at Dominican College of Tarlac</p>
            
           <div class="logo-row">
               <img src="assets/img/dct.svg" alt="College Logo" class="side-image">
               <img src="assets/img/ccs_resized_85x85.png" alt="CSS Image" class="side-image">
           </div>
        </div>

        <div class="right-panel d-flex flex-column justify-content-end">
            <div class="card">
                <div class="card-header text-center">
                    <h2>USER LOGIN</h2>
                    <hr>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="username">Email</label>
                            <input type="text" class="form-control" id="username" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group text-right">
                            <a href="{{ route('password.request') }}" class="btn btn-link">Forgot Password?</a>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Service Worker -->
    <script src="{{ asset('/sw.js') }}"></script>

    <script>
        if ("serviceWorker" in navigator) {
            // Register a service worker hosted at the root of the site using the default scope.
            navigator.serviceWorker.register("/sw.js").then(
                (registration) => {
                    console.log("Service worker registration succeeded:", registration);
                },
                (error) => {
                    console.error(`Service worker registration failed: ${error}`);
                }
            );
        } else {
            console.error("Service workers are not supported.");
        }
    </script>
</body>
</html>
