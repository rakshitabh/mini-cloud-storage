<?php
session_start();
include 'config.php';

// ✅ Check if user is logged in BEFORE any output
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Handle file upload
if(isset($_POST['upload'])){
    $file_name = $_FILES['file']['name'];
    $tmp_name = $_FILES['file']['tmp_name'];
    $target_dir = "uploads/";
    $file_path = $target_dir . time() . "_" . basename($file_name); // avoid overwrite

    if(move_uploaded_file($tmp_name, $file_path)){
        $stmt = $conn->prepare("INSERT INTO files (user_id, file_name, file_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $file_name, $file_path);
        $stmt->execute();
        $message = "✅ File uploaded successfully!";
    } else {
        $message = "❌ Failed to upload file!";
    }
}

// Handle file delete
if(isset($_GET['delete'])){
    $file_id = $_GET['delete'];
    $stmt = $conn->prepare("SELECT file_path FROM files WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $file_id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if($res){
        unlink($res['file_path']); // delete from server
        $stmt = $conn->prepare("DELETE FROM files WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $file_id, $user_id);
        $stmt->execute();
        $message = "✅ File deleted successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Mini Cloud Storage</title>
    <link rel="stylesheet" type="text/css" href="style.css"> <!-- ✅ CSS linked here -->
</head>
<body>
<div class="container">

    <h2>Welcome, <?php echo $name; ?></h2>

    <?php if(isset($message)) echo "<p style='color:green;'>$message</p>"; ?>

    <h3>Upload File</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit" name="upload">Upload</button>
    </form>

    <h3>Your Files</h3>
    <table>
        <tr>
            <th>File Name</th>
            <th>Upload Time</th>
            <th>Actions</th>
        </tr>
        <?php
        $stmt = $conn->prepare("SELECT * FROM files WHERE user_id=? ORDER BY upload_time DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()){
            echo "<tr>";
            echo "<td>{$row['file_name']}</td>";
            echo "<td>{$row['upload_time']}</td>";
            echo "<td>
                    <a href='{$row['file_path']}' download>Download</a> |
                    <a href='dashboard.php?delete={$row['id']}' onclick=\"return confirm('Delete this file?')\">Delete</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <a class="logout" href="logout.php">Logout</a>
</div>
</body>
</html>
