<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="{{ asset('assets/img/logo2.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#007bff">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Class Monitoring">
    <meta name="description" content="Login to Class Monitoring - College of Computer Studies">
</head>
<body>
    <style>
        .left-panel {
            width: 250px;
            background-color: transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .main-logo {
            width: 100%;
            max-width: 380px;
            height: auto;
            margin-bottom: 20px;
        }

        .left-panel p {
            font-size: 14px;
            text-align: center;
            margin-bottom: 0px;
        }

        .logo-row {
            display: flex;
            justify-content: space-between;
            width: 50%;
            margin-top: 20px;
        }

        .side-image {
            width: 100%;
            max-width: 100px;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .left-panel {
                width: 200px;
            }

            .main-logo {
                max-width: 150px;
            }

            .side-image {
                max-width: 60px;
            }

            .left-panel p {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .left-panel {
                width: 280px;
                padding-bottom: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .logo-row {
                display: flex;
                justify-content: space-between;
                width: 100%;
                margin-top: 20px;
            }

            .main-logo {
                max-width: 130px;
            }

            .side-image {
                max-width: 50px;
            }

            .left-panel p {
                font-size: 10px;
            }
        }
    </style>
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
</body>
</html>

