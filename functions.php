<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'Admin';
}

function isArtist() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'Artist';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>