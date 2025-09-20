<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $user_otp = $_POST['otp'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND otp=?");
    $stmt->bind_param("ss", $email, $user_otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $conn->query("UPDATE users SET is_verified=1, otp=NULL WHERE email='$email'");
        echo "✅ Email verified! You can now <a href='login.php'>Login</a>.";
    } else {
        echo "❌ Invalid email or OTP!";
    }
}
?>
<!DOCTYPE html>
<head>
    <title>Verify - Mini Cloud Storage</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h2>Verify Email</h2>
<form method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="text" name="otp" placeholder="Enter OTP" required><br>
    <button type="submit">Verify</button>
</form>
</body>
<html>

