<?php
include 'db_connect.php'; // Ensure correct database connection
session_start();

// Retrieve search parameters
$search_letter = isset($_GET['letter']) ? $_GET['letter'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_by_date = isset($_GET['date']) ? $_GET['date'] : '';
$filter_by_privacy = isset($_GET['privacy']) ? $_GET['privacy'] : '';

// Start building the query
$query = "SELECT * FROM blogs WHERE 1";

// Add letter-based filtering
if (!empty($search_letter)) {
    $query .= " AND title LIKE ?";
}

// Add date range filter
if (!empty($start_date) && !empty($end_date)) {
    $query .= " AND event_date BETWEEN ? AND ?";
}

// Add free-text search for title or description
if (!empty($search)) {
    $query .= " AND (title LIKE ? OR description LIKE ?)";
}

// Add specific event date filter
if (!empty($filter_by_date)) {
    $query .= " AND event_date = ?";
}

// Add privacy filter if provided
if (!empty($filter_by_privacy)) {
    $query .= " AND privacy_filter = ?";
}

// Prepare the query
$stmt = $conn->prepare($query);

// Bind parameters dynamically
$param_types = '';
$param_values = [];

if (!empty($search_letter)) {
    $param_types .= 's';
    $param_values[] = $search_letter . '%';
}
if (!empty($start_date) && !empty($end_date)) {
    $param_types .= 'ss';
    $param_values[] = $start_date;
    $param_values[] = $end_date;
}
if (!empty($search)) {
    $param_types .= 'ss';
    $param_values[] = "%$search%";
    $param_values[] = "%$search%";
}
if (!empty($filter_by_date)) {
    $param_types .= 's';
    $param_values[] = $filter_by_date;
}
if (!empty($filter_by_privacy)) {
    if ($filter_by_privacy === 'private' || $filter_by_privacy === 'public') {
        $param_types .= 's';
        $param_values[] = $filter_by_privacy;
    } else {
        echo "<p>Invalid privacy filter value.</p>";
        exit;
    }
}

if (!empty($param_types)) {
    $stmt->bind_param($param_types, ...$param_values);
}

$stmt->execute();
$result = $stmt->get_result();

// Display results
echo "<div class='blog-container'>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='blog-entry'>";
        echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        echo "<p><strong>Date of Event:</strong> " . htmlspecialchars($row['event_date']) . "</p>";
        echo "<p><strong>Privacy:</strong> " . htmlspecialchars($row['privacy_filter']) . "</p>";
        echo "</div>";
    }
} else {
    echo "<p>No results found.</p>";
}
echo "</div>";

$conn->close();
?>
