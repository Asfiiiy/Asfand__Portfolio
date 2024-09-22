<?php session_start();

// Hardcoded admin credentials
$adminUsername = "admin";
$adminPassword = "admin123";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the entered credentials match the hardcoded ones
    if ($username === $adminUsername && $password === $adminPassword) {
        $_SESSION['role'] = 'admin';
        header('Location: video.php');
    } else {
        echo "Invalid username or password!";
    }
}
?>

<form method="post">
    <label>Username: <input type="text" name="username" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <button type="submit">Login</button>
</form>
