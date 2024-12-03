<?php
// Include database connection
include 'db_connect.php';

// Determine sorting order based on user selection
$order = "event_date DESC"; // Default: Newest first
if (isset($_GET['sort'])) {
    if ($_GET['sort'] === "date_asc") $order = "event_date ASC";
    if ($_GET['sort'] === "title_asc") $order = "title ASC";
}

// Fetch public blogs sorted by the selected order
$sql = "SELECT * FROM blogs WHERE privacy_filter = '0' ORDER BY $order";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Blog Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Welcome to the Blog Dashboard</h1>
    </header>

    <div class="container">
        <!-- Sorting Options -->
        <form method="GET" class="sort-options">
            <label for="sort">Sort by:</label>
            <select name="sort" id="sort">
                <option value="date_desc">Newest First</option>
                <option value="date_asc">Oldest First</option>
                <option value="title_asc">Title (A-Z)</option>
            </select>
            <button type="submit">Apply</button>
        </form>

        <!-- Blog Container -->
        <div class="blog-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='blog-entry'>";
                    echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
                    echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                    echo "<small>Event Date: " . $row['event_date'] . "</small><br>";
                    echo "<a href='view_public_blogs.php?id=" . $row['blog_id'] . "' class='btn'>Read More</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No public blogs available at the moment.</p>";
            }
            ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Blog Dashboard. All rights reserved.</p>
    </footer>
</body>
</html>
