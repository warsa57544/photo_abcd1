<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "Please fill in all fields!";
        exit();
    }

    if ($action == 'register') {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if the email already exists
        $check_stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $check_stmt->bind_param('s', $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "Email is already registered!";
            exit();
        }

        // Register the user
        $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->bind_param('ss', $email, $hashed_password);
        if ($stmt->execute()) {
            echo "Registration successful! You can now log in.";
        } else {
            echo "Error during registration: " . $conn->error;
        }
        $stmt->close();

    } elseif ($action == 'login') {
        // Verify user credentials
        $stmt = $conn->prepare("SELECT email, password FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_email'] = $user['email']; // Store email in session
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Invalid credentials!";
            }
        } else {
            echo "Invalid credentials!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register or Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Register or Login</h1>
    </header>
    <div class="container">
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required><br><br>

            <input type="hidden" name="action" value="register">
            <button type="submit" onclick="this.form.action.value='register'">Register</button>
            <button type="submit" onclick="this.form.action.value='login'">Login</button>
        </form>
    </div>
</body>
</html>
