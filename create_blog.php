<?php
include 'config.php';
session_start();

// Enable error reporting for debugging (optional for development only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: register_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $event_date = $_POST['event_date'];
    $privacy_flag = isset($_POST['privacy_flag']) ? intval($_POST['privacy_flag']) : 0;

    // Default image
    $photo = 'default.png';

    // Handle file upload
    if (!empty($_FILES['photo']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['photo']['type'];

        if (in_array($file_type, $allowed_types)) {
            $target_dir = "uploads/";

            // Create the uploads directory if it doesn't exist
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $photo = time() . "_" . basename($_FILES['photo']['name']); // Generate a unique file name
            $target_file = $target_dir . $photo;

            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                echo "Error uploading file.";
                exit();
            }
        } else {
            echo "Invalid file type. Please upload JPG, PNG, or GIF images.";
            exit();
        }
    }

    // Validate title format
    if (!preg_match('/^[a-zA-Z0-9]/', $title)) {
        echo "Title must start with a letter or number!";
        exit();
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO blogs (user_email, creator_email, title, description, photo, privacy_flag, event_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }

    $user_email = $_SESSION['user_email'];
    $stmt->bind_param('sssssis', $user_email, $user_email, $title, $description, $photo, $privacy_flag, $event_date);

    if ($stmt->execute()) {
        header("Location: dashboard.php"); // Redirect to dashboard after success
        exit();
    } else {
        echo "Error creating blog: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Create a New Blog</h1>
    </header>
    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" placeholder="Enter blog title" required><br><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description" placeholder="Enter blog description" required></textarea><br><br>

            <label for="event_date">Event Date:</label>
            <input type="date" id="event_date" name="event_date" required><br><br>

            <label for="photo">Upload Photo:</label>
            <input type="file" id="photo" name="photo" accept="image/*"><br><br>

            <label for="privacy_flag">Privacy:</label>
            <select id="privacy_flag" name="privacy_flag">
                <option value="0">Public</option>
                <option value="1">Private</option>
            </select><br><br>

            <button type="submit">Create Blog</button>
        </form>
    </div>
</body>
</html>

