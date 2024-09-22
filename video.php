<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Connect to the MySQL database
$conn = new mysqli('localhost', 'root', '', 'video_gallery');

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request if the user is an admin
if ($isAdmin && isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Get the filename from the database
    $stmt = $conn->prepare("SELECT filename FROM videos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($filename);
    $stmt->fetch();
    $stmt->close();

    // Delete the file from the uploads directory
    $file_path = "uploads/" . $filename;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Delete the record from the database
    $stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Redirect to prevent resubmission
    header("Location: video.php");
    exit;
}

// Handle file upload if the user is an admin and the form is submitted
if ($isAdmin && isset($_FILES['video']) && isset($_POST['title'])) {
    $target_dir = "uploads/";
    
    // Ensure the uploads directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["video"]["name"]);
    $title = $_POST['title'];

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($_FILES["video"]["tmp_name"], $target_file)) {
        $filename = basename($_FILES["video"]["name"]);

        // Insert the video file name and title into the database
        $stmt = $conn->prepare("INSERT INTO videos (title, filename) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $filename);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

// Fetch all videos from the database
$result = $conn->query("SELECT * FROM videos");
if (!$result) {
    die("Error fetching videos: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Gallery</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- GOOGLE FONTS -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Play&display=swap" rel="stylesheet"> 
  
</head>
<body>
<div class="wrapper">
    <nav class="main-nav side">
        <a href="login.php" class="nav-logo">
            <img src="hk.jpg" class="img-fluid rounded-circle" alt="Responsive image">
            <span class="nav-name">Asfand Yar</span>
        </a>
        <button class="nav-toggle" aria-label="Toggle navigation">☰</button>
        <ul class="nav-list">
            <li><a href="index2.html">Home 🏠︎</a></li>
            <li><a href="project.php">Projects 📋</a></li>
            <li><a href="video.php">Videos ▶️</a></li>
            <li><a href="about.html">About 💬</a></li>
            <li><a href="contactus.html">Contact 📧</a></li>
        </ul>
    </nav>

    <h1 class="vhead">Video Gallery</h1>

    <div class="video-gallery">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="video-item">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3> <!-- Display video title -->
            <video controls>
                <source src="uploads/<?php echo htmlspecialchars($row['filename']); ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <?php if ($isAdmin): ?>
            <form action="" method="get" onsubmit="return confirm('Are you sure you want to delete this video?');">
                <input type="hidden" name="delete" value="<?php echo $row['id']; ?>">
                <button type="submit" class="delete-button">Delete</button>
            </form>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
        <!--  -->
        <div class="formi" <?php if (!$isAdmin) echo 'style="display:none !important;"'; ?> >
            <?php if ($isAdmin): ?>
             <form action="video.php" method="post" enctype="multipart/form-data">
         <label for="title" class="title-label">Title:</label>
         <input type="text" name="title" id="title" required>
         <label for="video">Upload Video:</label>
         <input type="file" name="video" id="video" accept="video/*" required>
         <button type="submit">Upload</button>
                 </form>
         <?php endif; ?>
        </div>


    </div>
</div>

<footer>
    <div class="footer">
        <div class="row">
            <a href="#"><i class="fa fa-facebook"></i></a>
            <a href="#"><i class="fa fa-instagram"></i></a>
            <a href="www.linkedin.com/in/asfand-yar-b937a9231"><i class="fa fa-linkedin"></i></a>
            <a href="#"><i class="fa fa-twitter"></i></a>
        </div>
        <div class="row">
            <ul>
                <li><a href="#">Contact us</a></li>
                <li><a href="#">Our Services</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">Career</a></li>
            </ul>
        </div>
        <div class="row">
            Webdeveloper Copyright © 2024 - All rights reserved || Designed By: Asfand Yar
        </div>
    </div>
</footer>

<script src="script2.js"></script>
</body>
</html>
