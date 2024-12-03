<?php
// Include the database connection
include 'config.php'; // Use 'config.php' to ensure consistent database configuration

// Check if a specific blog ID is requested
if (isset($_GET['id'])) {
    $blog_id = intval($_GET['id']); // Sanitize input
    $query = "SELECT * FROM blogs WHERE blog_id = $blog_id AND privacy_filter = '0'"; // Fetch only public blog
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Display specific blog details
        $row = $result->fetch_assoc();
        echo "<div class='blog-entry'>";
        echo "<h1>" . htmlspecialchars($row['title']) . "</h1>";
        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        echo "<p><strong>Date of Event:</strong> " . htmlspecialchars($row['event_date']) . "</p>";
        echo "</div>";
    } else {
        echo "<p>Blog not found or it is private.</p>";
    }
} else {
    // If no specific blog ID is requested, display all public blogs
    $query = "SELECT * FROM blogs WHERE privacy_filter = '0' ORDER BY event_date DESC";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<div class='blog-container'>";
        while ($row = $result->fetch_assoc()) {
            echo "<div class='blog-entry'>";
            echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
            echo "<p>" . htmlspecialchars(substr($row['description'], 0, 150)) . "...</p>";
            echo "<p><strong>Date of Event:</strong> " . htmlspecialchars($row['event_date']) . "</p>";
            echo "<a href='view_public_blogs.php?id=" . $row['blog_id'] . "' class='btn'>Read More</a>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<p>No public blogs available.</p>";
    }
}

// Close the database connection
$conn->close();
?>
