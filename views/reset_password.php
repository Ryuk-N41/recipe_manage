<?php
// views/reset_password.php
session_start();

// CORRECT PATH for your structure
require_once __DIR__ . '/../config/db.php';  // Fix this line

$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$query = "UPDATE users SET password = ? WHERE username = 'admin'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $hashed_password);

if (mysqli_stmt_execute($stmt)) {
    echo "<h3>Admin password reset successfully!</h3>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>New Password:</strong> $new_password</p>";
    echo "<p><a href='login.php'>Go to Login</a></p>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
