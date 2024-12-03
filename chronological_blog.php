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

// Determine the sorting order based on user selection
$order = isset($_GET['order']) && $_GET['order'] === 'DESC' ? 'DESC' : 'ASC';

// Fetch blogs sorted by event date based on selected order
$query = "SELECT title, description, photo, event_date FROM blogs WHERE privacy_flag = 0 ORDER BY event_date $order";
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
    <title>Chronological Blog Compilation</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Chronological Blog Compilation</h1>
    </header>

    <!-- Sorting Options -->
    <form method="GET" action="chronological_blog.php" class="sort-options">
        <label for="order">Sort by:</label>
        <select name="order" id="order">
            <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>Oldest First</option>
            <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>Newest First</option>
        </select>
        <button type="submit">Sort</button>
    </form>

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
