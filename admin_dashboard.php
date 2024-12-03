<?php
include 'config.php';
session_start();

// Ensure only admins can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: register_login.php");
    exit();
}

// Fetch summary report
$summaryQuery = "SELECT user_email, COUNT(*) AS total_blogs FROM blogs GROUP BY user_email";
$summaryResult = $conn->query($summaryQuery);

// Fetch status report for alphabet coverage
$statusQuery = "SELECT user_email, GROUP_CONCAT(LEFT(title, 1)) AS initials FROM blogs GROUP BY user_email";
$statusResult = $conn->query($statusQuery);

// Helper function to determine missing letters
function missingLetters($coveredLetters) {
    $allLetters = range('A', 'Z');
    $coveredLetters = array_unique(array_map('strtoupper', str_split($coveredLetters)));
    return array_diff($allLetters, $coveredLetters);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
    </header>

    <section class="dashboard-section">
        <h2>Summary Report</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User Email</th>
                    <th>Total Blogs</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $summaryResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_email']) ?></td>
                        <td><?= htmlspecialchars($row['total_blogs']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <section class="dashboard-section">
        <h2>Status Report (Alphabet Coverage)</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User Email</th>
                    <th>Covered Letters</th>
                    <th>Missing Letters</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $statusResult->fetch_assoc()): ?>
                    <?php
                    $coveredLetters = implode('', $row['initials']);
                    $missing = missingLetters($coveredLetters);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_email']) ?></td>
                        <td><?= htmlspecialchars(implode(', ', array_unique(str_split($coveredLetters)))) ?></td>
                        <td><?= htmlspecialchars(implode(', ', $missing)) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <footer>
        <p>© <?= date("Y") ?> Your Website</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
