<?php
include 'config.php';
session_start();

// Enable error reporting for debugging (optional for development only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in (optional, if required for access)
if (!isset($_SESSION['user_email'])) {
    header("Location: register_login.php");
    exit();
}

// Fetch blogs sorted alphabetically
$query = "SELECT title, description, photo, event_date FROM blogs WHERE privacy_flag = 0 ORDER BY title ASC";
$result = $conn->query($query);

if (!$result) {
    echo "Error fetching blogs: " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alphabetical Blog Compilation</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Alphabetical Blog Compilation</h1>
    </header>
    <div class="blog-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="blog-entry">
                    <h2><?= htmlspecialchars($row['title']) ?></h2>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <p><strong>Event Date:</strong> <?= htmlspecialchars($row['event_date']) ?></p>
                    <?php if (!empty($row['photo'])): ?>
                        <img src="uploads/<?= htmlspecialchars($row['photo']) ?>" alt="Blog Image" style="max-width: 100%; height: auto;">
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No blogs available to display.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>

