<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to fetch user data
    $sql = "SELECT id FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // If user exists, set session variable and redirect to dashboard
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password";
        header("Location: login.php?error=$error");
        exit;
    }
}
$conn->close();
?>
