<?php

// Include database configuration file
require_once '../../app/db-config.php';

// Connect to database
$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

// Check if the form was submitted
if (isset($_POST['submit'])) {
    // Get the form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check if the user is locked out
    require_once 'limit-logins.php';
    if (checkLoginAttempts($email)) {
        echo 'Your account is temporarily locked. Please try again later.';
        exit;
    }

    // Check if the user exists in the database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        // Get the user data
        $user = mysqli_fetch_assoc($result);

        // Check if the password is correct
        if (password_verify($password, $user['password'])) {
            // Start session
            session_start();

            // Login success
            resetLoginAttempts($email);
            $_SESSION['logged_in'] = true;
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['id'];

            // Generate the token
            if (!isset($_SESSION['user_token'])) {
                $_SESSION['user_token'] = bin2hex(random_bytes(32));
            }

            header('Location: ../profile.php');
            exit;
        } else {
            // Login failed
            updateLoginAttempts($email);
            echo 'Incorrect email or password. Please try again.';
        }
    } else {
        // User not found
        echo 'User not found. Please register.';
    }
}

mysqli_close($conn);

?>