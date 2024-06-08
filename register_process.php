<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if username or email already exists
    $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $check_result = $conn->query($check_query);
    if ($check_result->num_rows > 0) {
        echo "Username or email already exists.";
    } else {
        // Insert user into database
        $insert_query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($insert_query) === TRUE) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $insert_query . "<br>" . $conn->error;
        }
    }
    $conn->close();
}
?>
