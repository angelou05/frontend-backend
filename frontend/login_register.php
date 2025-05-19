<?php
    session_start();
    require_once 'config.php';

    // Function to handle notifications
    function showNotification($message, $type = 'success') {
        $_SESSION['notification'] = [
            'message' => $message,
            'type' => $type
        ];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle signup
        if (isset($_POST['signup'])) {
            $name = $_POST['name-input'];
            $email = $_POST['email-input'];
            $password = password_hash($_POST['password-input'], PASSWORD_DEFAULT);

            // Check if email already exists
            $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($connect, $checkEmailQuery);

            if (!$result) {
                showNotification("Database error: " . mysqli_error($connect), "error");
            } elseif (mysqli_num_rows($result) > 0) {
                showNotification("Email already exists", "error");
            } else {
                $insertQuery = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
                if (mysqli_query($connect, $insertQuery)) {
                    showNotification("Account created successfully!");
                    header("Location: login.php");
                    exit();
                } else {
                    showNotification("Database error: " . mysqli_error($connect), "error");
                }
            }
        }

        // Handle login
        if (isset($_POST['login'])) {
            $email = $_POST['email-input'];
            $password = $_POST['password-input'];

            // Check if email exists
            $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($connect, $checkEmailQuery);

            if (!$result) {
                showNotification("Database error: " . mysqli_error($connect), "error");
            } elseif (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_name'] = $row['name'];
                    $_SESSION['user_email'] = $row['email'];
                    $_SESSION['user_role'] = $row['role']; // Store user role in session
                    showNotification("Successfully logged in!");
                    header("Location: index.php");
                    exit();
                } else {
                    showNotification("Invalid password", "error");
                }
            } else {
                showNotification("Email not found", "error");
            }
        }
    }
?>