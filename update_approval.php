<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn() || $_SESSION['user_type'] != 'Admin') {
    redirect('login.php');
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $song_id = (int)$_GET['id'];

    if ($action == 'approve' || $action == 'reject') {
        $approval = ($action == 'approve') ? 1 : 0;

        // Update the approval status of the song
        $sql = "UPDATE Song SET approval = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $approval, $song_id);
            if (mysqli_stmt_execute($stmt)) {
                redirect('approve_songs.php');
            } else {
                echo "Error updating approval status: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        echo "Invalid action.";
    }
} else {
    echo "Invalid parameters.";
}

mysqli_close($conn);
?>
