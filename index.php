<?php
require_once 'config.php';
require_once 'functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Phonic Hub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 200px;
            background-color: #1a1a1a;
            padding: 20px;
            color: #fff;
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
            background-color: #121212;
            color: #fff;
        }
        .logo img {
            width: 150px;
            height: auto;
            margin-bottom: 20px;
        }
        .welcome-box {
            background-color: #282828;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            max-width: 500px;
            margin: 50px auto;
        }
        .welcome-box h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        .welcome-box p {
            font-size: 1.1em;
            margin-bottom: 30px;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1db954;
            color: #fff;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #1ed760;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <img src="logo.png" alt="Phonic Hub Logo">
            </div>
        </div>
        <div class="main-content">
            <div class="welcome-box">
                <h1>Welcome to Phonic Hub</h1>
                <p>Discover, stream, and listen to your favorite music. Join our community of music lovers today!</p>
                <div class="buttons">
                    <a href="login.php" class="btn">Login</a>
                    <a href="register.php" class="btn">Register</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>