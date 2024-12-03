<?php
// Database Configuration
$servername = "localhost";
$username = "root";
$password = ""; // Update with your MySQL password if necessary
$dbname = "photo_abcd"; // Update with your database name if necessary

// Establishing a Connection to the Database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for Connection Errors
if ($conn->connect_error) {
    // Log the error for debugging (optional, in case of sensitive data, remove this)
    error_log("Database Connection Error: " . $conn->connect_error);
    // Display a user-friendly error message
    die("We are experiencing technical difficulties. Please try again later.");
}

// Setting the Character Set to UTF-8 for International Support
if (!$conn->set_charset("utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . $conn->error);
    die("Error configuring database connection.");
}

// Optional: Enable Strict Mode for Better SQL Compatibility
if (!$conn->query("SET sql_mode = 'STRICT_ALL_TABLES'")) {
    error_log("Failed to set SQL mode: " . $conn->error);
}
?>
