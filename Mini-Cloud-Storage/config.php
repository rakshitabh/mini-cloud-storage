<?php
$host = "localhost";   
$user = "root";        // your MySQL username
$pass = "";  // leave "" if no password
$dbname = "cloud_storage";     // the database we created

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
