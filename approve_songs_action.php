<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn() || $_SESSION['user_type'] != 'Admin') {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve'])) {
        $song_id = intval($_POST['approve']);
        
        // Approve the song
        $sql = "UPDATE Song SET approval = 1 WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $song_id);
            if (mysqli_stmt_execute($stmt)) {
                echo "Song approved successfully.";
            } else {
                echo "Error approving the song.";
            }
            mysqli_stmt_close($stmt);
        }
    } elseif (isset($_POST['reject'])) {
        $song_id = intval($_POST['reject']);
        
        // Reject (delete) the song from the database
        $sql = "DELETE FROM Song WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $song_id);
            if (mysqli_stmt_execute($stmt)) {
                echo "Song rejected and removed successfully.";
            } else {
                echo "Error rejecting the song.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Redirect back to approve songs page after action
header("Location: approve_songs.php");
mysqli_close($conn);
?>
