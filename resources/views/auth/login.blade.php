<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Include Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .google-btn {
            background-color: #db4437;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 10px;
            text-transform: uppercase;
            font-weight: bold;
            border-radius: 4px;
            margin-top: 20px;
        }

        .google-btn i {
            margin-right: 10px;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }

        .login-container h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="login-container">
        <h2 class="text-center">Login</h2>

        <!-- Google Login Button -->
        <a href="{{ route('google.login') }}" class="google-btn">
            <i class="fab fa-google"></i> Login with Google
        </a>

        <!-- Optional: Traditional Email & Password Login (can be implemented later) -->
        <div class="text-center mt-3">
            <small>or</small>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div
