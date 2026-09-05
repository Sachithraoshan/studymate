<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return is_logged_in() && $_SESSION['role'] === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: /studymate/login.php");
        exit;
    }
}

function require_admin() {
    if (!is_admin()) {
        header("Location: /studymate/login.php");
        exit;
    }
}

function require_student() {
    if (!is_logged_in() || $_SESSION['role'] !== 'student') {
        header("Location: /studymate/login.php");
        exit;
    }
}

// Basic sanitisation helper
function clean($conn, $value) {
    return htmlspecialchars(trim($conn->real_escape_string($value)));
}
?>
