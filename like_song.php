<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$song_id = isset($_POST['song_id']) ? intval($_POST['song_id']) : 0;

if ($song_id > 0) {
    // Check if the user has already liked the song
    $sql = "SELECT * FROM Likes WHERE user_id = ? AND song_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $song_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            // If not liked already, insert into Likes table
            $sql = "INSERT INTO Likes (user_id, song_id) VALUES (?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ii", $user_id, $song_id);
                mysqli_stmt_execute($stmt);
            }
        } else {
            // If already liked, remove the like
            $sql = "DELETE FROM Likes WHERE user_id = ? AND song_id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ii", $user_id, $song_id);
                mysqli_stmt_execute($stmt);
            }
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
// Redirect back to dashboard or previous page
redirect('dashboard.php');
?>