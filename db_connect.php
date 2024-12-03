<?php
include 'db_connect.php';
// Your database queries and logic here

// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Update with your MySQL password if necessary
$dbname = "photo_abcd"; // Update with your actual database name

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    // Log error for debugging
    error_log("Database connection failed: " . $conn->connect_error);
    // Display user-friendly error message
    die("We are experiencing technical issues. Please try again later.");
}

// Set the character set to UTF-8
if (!$conn->set_charset("utf8")) {
    error_log("Error loading character set utf8: " . $conn->error);
}
?>
