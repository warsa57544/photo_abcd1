<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: register_login.php");
    exit();
}

$user_email = $_SESSION['user_email'];
$action = $_POST['action'] ?? ''; // 'set_private', 'set_public', 'update_blog'
$blog_id = $_POST['blog_id'] ?? null;

// Validate action
if (!in_array($action, ['set_private', 'set_public', 'update_blog'])) {
    echo "Invalid action.";
    exit();
}

// Handle the requested action
if ($action === 'set_private') {
    // Set all blogs to private
    $stmt = $conn->prepare("UPDATE blogs SET privacy_flag = 1 WHERE user_email = ?");
    $stmt->bind_param('s', $user_email);
    $stmt->execute();
    echo "All blogs set to private.";
} elseif ($action === 'set_public') {
    // Set all blogs to public
    $stmt = $conn->prepare("UPDATE blogs SET privacy_flag = 0 WHERE user_email = ?");
    $stmt->bind_param('s', $user_email);
    $stmt->execute();
    echo "All blogs set to public.";
} elseif ($action === 'update_blog' && $blog_id) {
    // Update privacy for a specific blog
    $privacy_flag = $_POST['privacy_flag'] ?? 0;
    $stmt = $conn->prepare("UPDATE blogs SET privacy_flag = ? WHERE blog_id = ? AND user_email = ?");
    $stmt->bind_param('iis', $privacy_flag, $blog_id, $user_email);
    $stmt->execute();
    echo "Blog privacy updated.";
}

$stmt->close();
$conn->close();
?>
