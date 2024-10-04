<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn() || $_SESSION['user_type'] != 'Artist') {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['song_file']) && isset($_FILES['cover_photo']) && !empty($_POST['title'])) {
    $user_id = $_SESSION['user_id'];
    $title = sanitizeInput($_POST['title']);
    $release_date = sanitizeInput($_POST['release_date']);
    $language = sanitizeInput($_POST['language']);
    $details = sanitizeInput($_POST['details']);
    $genre = sanitizeInput($_POST['genre']);
    $song_file = $_FILES['song_file'];
    $cover_photo = $_FILES['cover_photo'];

    // Validate song file
    if ($song_file['error'] !== UPLOAD_ERR_OK) {
        echo "Error uploading song file: " . $song_file['error'];
        exit;
    }

    $allowed_song_types = ['audio/mpeg'];
    if (!in_array($song_file['type'], $allowed_song_types)) {
        echo "Invalid song file type. Only MP3 files are allowed.";
        exit;
    }

    // Validate cover photo
    if ($cover_photo['error'] !== UPLOAD_ERR_OK) {
        echo "Error uploading cover photo: " . $cover_photo['error'];
        exit;
    }

    $allowed_photo_types = ['image/jpeg', 'image/png'];
    if (!in_array($cover_photo['type'], $allowed_photo_types)) {
        echo "Invalid cover photo type. Only JPEG and PNG files are allowed.";
        exit;
    }

    $upload_dir = 'uploads/';
    $song_file_name = basename($song_file['name']);
    $cover_file_name = basename($cover_photo['name']);
    $song_file_path = $upload_dir . $song_file_name;
    $cover_file_path = $upload_dir . $cover_file_name;

    // Check if the files already exist
    if (file_exists($song_file_path) || file_exists($cover_file_path)) {
        echo "One or both files already exist. Please rename the files.";
        exit;
    }

    // Attempt to move the uploaded files
    if (move_uploaded_file($song_file['tmp_name'], $song_file_path) && move_uploaded_file($cover_photo['tmp_name'], $cover_file_path)) {
        // Insert song metadata into database
        $sql = "INSERT INTO Song (URL, cover_photo_url, Release_date, language, details, title, approval, genre, artist_id) VALUES (?, ?, ?, ?, ?, ?, FALSE, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssssi", $song_file_path, $cover_file_path, $release_date, $language, $details, $title, $genre, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                echo "Song and cover photo uploaded successfully.";
            } else {
                echo "Error inserting song metadata: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to move uploaded files.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Song</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
        }
        .container {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
        }
        .sidebar {
            flex: 1;
            background: #1a1a1a;
            color: #121212;
            padding: 20px;
        }
        .sidebar .logo img {
            width: 150px;
            height: auto;
        }
        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }
        .sidebar nav ul li {
            margin: 10px 0;
        }
        .sidebar nav ul li a {
            color: #fff;
            text-decoration: none;
        }
        .main-content {
            flex: 3;
            padding: 20px;
            background: #121212;
            border-left: 0px solid #ccc;
        }
        .main-content h1 {
            margin-bottom: 20px;
            color: #fff;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="date"],
        textarea,
        input[type="file"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            padding: 10px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <img src="logo.png" alt="Phonic Hub Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Home</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="upload.php">Upload Song</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="main-content">
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <h1>Upload New Song</h1>
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>
                
                <label for="release_date">Release Date:</label>
                <input type="date" name="release_date" id="release_date" required>
                
                <label for="language">Language:</label>
                <input type="text" name="language" id="language" required>
                
                <label for="details">Details:</label>
                <textarea name="details" id="details" rows="4" required></textarea>
                
                <label for="genre">Genre:</label>
                <input type="text" name="genre" id="genre" required>
                
                <label for="song_file">Upload MP3:</label>
                <input type="file" name="song_file" id="song_file" accept="audio/mpeg" required>
                
                <label for="cover_photo">Upload Cover Photo:</label>
                <input type="file" name="cover_photo" id="cover_photo" accept="image/jpeg,image/png" required>
                
                <button type="submit">Upload</button>
            </form>
        </div>
    </div>
</body>
</html>
