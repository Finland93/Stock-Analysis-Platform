<?php

// Load config + shared helpers, then start a hardened session.
require_once '../../app/db-config.php';
secure_session();

// Connect to database
$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (!$conn) {
    error_log('Stock platform login: DB connection failed: ' . mysqli_connect_error());
    die('A database error occurred. Please try again later.');
}

// Check if the form was submitted
if (isset($_POST['submit'])) {

    // CSRF protection
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        mysqli_close($conn);
        die('Invalid or expired request. Please reload the page and try again.');
    }

    // Get the form data
    $email    = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    // Check if the user is locked out
    require_once 'limit-logins.php';
    if (checkLoginAttempts($email)) {
        echo 'Your account is temporarily locked. Please try again later.';
        mysqli_close($conn);
        exit;
    }

    // Look up the user with a prepared statement (prevents SQL injection)
    $stmt = mysqli_prepare($conn, 'SELECT id, password, role FROM users WHERE email = ? LIMIT 1');
    if (!$stmt) {
        error_log('Stock platform login: prepare failed: ' . mysqli_error($conn));
        mysqli_close($conn);
        die('A database error occurred. Please try again later.');
    }
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);

    // Verify the password against the stored hash
    if ($user && password_verify($password, $user['password'])) {
        // Prevent session fixation on privilege change
        session_regenerate_id(true);

        resetLoginAttempts($email);
        $_SESSION['logged_in'] = true;
        $_SESSION['role']      = $user['role'];
        $_SESSION['user_id']   = $user['id'];

        if (!isset($_SESSION['user_token'])) {
            $_SESSION['user_token'] = bin2hex(random_bytes(32));
        }

        // Transparently upgrade the hash if PHP's default cost/algorithm changed
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            if ($up = mysqli_prepare($conn, 'UPDATE users SET password = ? WHERE id = ?')) {
                mysqli_stmt_bind_param($up, 'si', $new_hash, $user['id']);
                mysqli_stmt_execute($up);
                mysqli_stmt_close($up);
            }
        }

        mysqli_close($conn);
        header('Location: ../profile.php');
        exit;
    } else {
        // Generic message so we don't reveal whether the email is registered
        updateLoginAttempts($email);
        echo 'Incorrect email or password. Please try again.';
    }
}

mysqli_close($conn);
