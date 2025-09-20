<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $otp = rand(100000, 999999); // 6-digit OTP

    // Validate email format
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "❌ Invalid email format!";
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "❌ Email already registered!";
    } else {
        // Insert user with OTP
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, otp, is_verified) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("ssss", $name, $email, $password, $otp);
        if ($stmt->execute()) {
            $_SESSION['email'] = $email;
            echo "✅ Signup successful! Your OTP (for testing) is: <b>$otp</b><br>";
            echo "<a href='verify.php'>Go to Verify Page</a>";
        } else {
            echo "❌ Error: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" type="text/css" href="style.css"></head>
<body>
<h2>Signup</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Full Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Sign Up</button>
</form>
</body>
</html>