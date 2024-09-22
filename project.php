<?php
// Start a session only if one is not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Connect to the database for the project gallery
$conn_projects = new mysqli('localhost', 'root', '', 'project_gallery');

// Check for connection errors
if ($conn_projects->connect_error) {
    die("Connection failed: " . $conn_projects->connect_error);
}

// Handle file upload if the user is an admin and the form is submitted
if ($isAdmin && isset($_FILES['project']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['link'])) {
    $target_dir = "projects/";

    // Ensure the projects directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["project"]["name"]);

    // Move the uploaded file to the projects directory
    if (move_uploaded_file($_FILES["project"]["tmp_name"], $target_file)) {
        $filename = basename($_FILES["project"]["name"]);
        $title = $_POST['title'];
        $description = $_POST['description'];
        $link = $_POST['link']; // Get the link from the form

        // Prepare the SQL statement
        $stmt = $conn_projects->prepare("INSERT INTO projects (title, description, filename, link) VALUES (?, ?, ?, ?)");
        
        // Check if the prepare statement failed
        if ($stmt === false) {
            die("Error preparing SQL statement: " . $conn_projects->error);
        }

        // Bind the parameters and execute the statement
        $stmt->bind_param("ssss", $title, $description, $filename, $link);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}


// Handle deletion of a project file if the user is an admin and deletion is requested
if ($isAdmin && isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Prepare the SQL statement
    $stmt = $conn_projects->prepare("SELECT filename FROM projects WHERE id = ?");
    
    // Check if the prepare statement failed
    if ($stmt === false) {
        die("Error preparing SQL statement: " . $conn_projects->error);
    }

    // Bind and execute the statement
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($filename);
    $stmt->fetch();
    $stmt->close();

    // Delete the file from the directory
    if ($filename && file_exists("projects/" . $filename)) {
        unlink("projects/" . $filename);
    }

    // Prepare and execute the delete statement
    $stmt = $conn_projects->prepare("DELETE FROM projects WHERE id = ?");
    
    // Check if the prepare statement failed
    if ($stmt === false) {
        die("Error preparing SQL statement: " . $conn_projects->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all projects from the database
$result = $conn_projects->query("SELECT * FROM projects");
if (!$result) {
    die("Error fetching projects: " . $conn_projects->error);
}

$conn_projects->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Gallery</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <!-- GOOGLE FONTS -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Play&display=swap" rel="stylesheet">
    <!-- <style>
body {
    align-items: center;
    justify-content: center;  
    font-family: sans-serif;
}

    </style> -->
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
        
        <!-- <div class="area">
            <div class="circles">
                <i></i>
                <i></i>
                <i></i>
                <i></i>
                <i></i>
                <i></i>
                <i></i>
                <i></i>
                <i></i>
                <i></i>
            </div>
        </div> -->

        <!-- Project Gallery Header -->
        <div class="vhead"><h1>Project Gallery</h1></div>

        <!-- Automatically Generated Project Cards -->
        <div class="containerz">
    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="card">
        <div class="slide slide1">
            <div class="content">
                <div class="icon">
                    <img src="projects/<?php echo htmlspecialchars($row['filename']); ?>" alt="..." class="img-fluid img-thumbnail" width="100%">
                </div>
            </div>
        </div>
        <div class="slide slide2">
            <div class="content">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <a href="<?php echo htmlspecialchars($row['link']); ?>" target="_blank">View Project</a>
                <?php if ($isAdmin): ?>
                <a href="project.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this project?');">Delete</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>


        <!-- Admin Upload Form -->
        
        <div class="formproject" <?php if (!$isAdmin) echo 'style="display:none !important;"'; ?>>
    <div class="formiprojects"> 
        <?php if ($isAdmin): ?>
            <form action="project.php" method="post" enctype="multipart/form-data">
    <label for="title">Title:</label>
    <input type="text" name="title" id="title" required>
    
    <label for="description">Description:</label>
    <textarea name="description" id="description" required></textarea>
    
    <label for="project">Upload Project:</label>
    <input type="file" name="project" id="project" accept=".zip,.rar,.pdf,.docx,.jpg,.png" required>
    
    <label for="link">Project Link:</label>
    <input type="url" name="link" id="link" placeholder="http://example.com" required>
    
    <button type="submit">Upload</button>
</form>

        <?php endif; ?>
    </div>
</div>
    </div>

    <!-- Footer -->
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
