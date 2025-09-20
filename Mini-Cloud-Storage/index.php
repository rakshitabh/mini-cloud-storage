<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<h2>Welcome, <?php echo $_SESSION['name']; ?> 👋</h2>
<a href="logout.php">Logout</a>

<h3>Your Files</h3>
<!-- We will list files here after we add upload feature -->
<a href="upload.php">Upload New File</a>
