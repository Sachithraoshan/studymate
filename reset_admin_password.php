<?php
// Run this file ONCE in your browser after importing studymate.sql
// e.g. http://localhost/studymate/reset_admin_password.php
// It sets the admin password to: admin123
// DELETE THIS FILE after running it once, for security.

require_once __DIR__ . '/config/db.php';

$newPassword = 'admin123';
$hash = password_hash($newPassword, PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = 'admin@studymate.com' AND role = 'admin'");
$stmt->bind_param("s", $hash);

if ($stmt->execute()) {
    echo "Admin password has been reset successfully.<br>";
    echo "Login with: <b>admin@studymate.com</b> / <b>admin123</b><br>";
    echo "<strong>Please delete this file (reset_admin_password.php) now.</strong>";
} else {
    echo "Failed to reset password: " . $conn->error;
}
?>
