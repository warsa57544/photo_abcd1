<?php
include 'config.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: register_login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Fetch the user's blogs
$blogsQuery = $conn->prepare("SELECT blog_id, title, description, event_date, privacy_flag FROM blogs WHERE user_email = ?");
if (!$blogsQuery) {
    die("Error preparing blogs query: " . $conn->error);
}
$blogsQuery->bind_param('s', $user_email);
$blogsQuery->execute();
$blogsResult = $blogsQuery->get_result();

// Fetch progress for Alphabet Book
$alphabetCount = ['count' => 0]; // Default count
$alphabetQuery = $conn->prepare("SELECT COUNT(*) AS count FROM alphabet_books WHERE user_email = ?");
if ($alphabetQuery) {
    $alphabetQuery->bind_param('s', $user_email);
    $alphabetQuery->execute();
    $alphabetResult = $alphabetQuery->get_result();
    $alphabetCount = $alphabetResult->fetch_assoc();
}

// Ensure there is always a valid count value
if (!isset($alphabetCount['count'])) {
    $alphabetCount['count'] = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Welcome to Your Dashboard</h1>
    </header>

    <section class="dashboard-section">
        <h2>Your Blogs</h2>
        <form method="POST" action="manage_blog_privacy.php" class="bulk-actions">
            <button type="submit" name="action" value="set_public">Make All Blogs Public</button>
            <button type="submit" name="action" value="set_private">Make All Blogs Private</button>
        </form>
        <div class="blog-container">
            <?php if ($blogsResult->num_rows > 0): ?>
                <?php while ($blog = $blogsResult->fetch_assoc()): ?>
                    <div class="blog-entry">
                        <h3><?= htmlspecialchars($blog['title']) ?></h3>
                        <p><?= htmlspecialchars($blog['description']) ?></p>
                        <small>Event Date: <?= htmlspecialchars($blog['event_date']) ?></small>
                        <form method="POST" action="manage_blog_privacy.php" class="blog-privacy-form">
                            <input type="hidden" name="action" value="update_blog">
                            <input type="hidden" name="blog_id" value="<?= $blog['blog_id'] ?>">
                            <label for="privacy_flag_<?= $blog['blog_id'] ?>">Privacy:</label>
                            <select name="privacy_flag" id="privacy_flag_<?= $blog['blog_id'] ?>">
                                <option value="0" <?= $blog['privacy_flag'] == 0 ? 'selected' : '' ?>>Public</option>
                                <option value="1" <?= $blog['privacy_flag'] == 1 ? 'selected' : '' ?>>Private</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No blogs found. Start creating blogs!</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="dashboard-section">
        <h2>Your Alphabet Book Progress</h2>
        <p>
            You have added <strong><?= $alphabetCount['count'] ?></strong> entries to your Alphabet Book.
        </p>
        <?php if ($alphabetCount['count'] === 0): ?>
            <p>You haven't started creating your Alphabet Book yet.</p>
        <?php endif; ?>
    </section>

    <footer>
        <p>© <?= date("Y") ?> Your Website</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
