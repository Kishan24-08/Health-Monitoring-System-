<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $parameter_name = $_POST["parameter_name"];
    $description = $_POST["des"];
    
    // Validate form data (you can add more validation if needed)
    if (empty($parameter_name) || empty($description)) {
        // Handle empty fields
        echo "Please fill in all fields.";
    } else {
        // Sanitize data
        $parameter_name = mysqli_real_escape_string($conn, $parameter_name);
        $description = mysqli_real_escape_string($conn, $description);
        $user_id = $_SESSION['user_id'];
        
        // Insert data into database
        $sql = "INSERT INTO health_data (user_id, parameter_name, des,	date_added) VALUES ('$user_id', '$parameter_name', '$description', NOW())";
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php");

        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
} else {
    // Handle invalid request
    echo "Invalid request.";
}

// Close database connection
$conn->close();
?>
