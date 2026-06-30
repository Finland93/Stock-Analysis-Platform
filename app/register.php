<?php

// Load config + shared helpers, then start a hardened session.
require_once '../../app/db-config.php';
secure_session();

// Already logged in? Go to profile.
if (isset($_SESSION['user_id'])) {
    header('Location: ../profile.php');
    exit;
}

// Connect to database
$db_connection = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (!$db_connection) {
    error_log('Stock platform register: DB connection failed: ' . mysqli_connect_error());
    die('A database error occurred. Please try again later.');
}

// Check if form is submitted
if (isset($_POST['submit'])) {

    // CSRF protection
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        mysqli_close($db_connection);
        die('Invalid or expired request. Please reload the page and try again.');
    }

    // Collect and trim input (kept raw here; binding handles SQL safety)
    $full_name    = trim((string) ($_POST['full_name'] ?? ''));
    $full_address = trim((string) ($_POST['full_address'] ?? ''));
    $country      = trim((string) ($_POST['country'] ?? ''));
    $region       = trim((string) ($_POST['region'] ?? ''));
    $zip_code     = trim((string) ($_POST['zip_code'] ?? ''));
    $email        = trim((string) ($_POST['email'] ?? ''));
    $password_raw = (string) ($_POST['password'] ?? '');

    // Basic validation
    if ($full_name === '' || $full_address === '' || $country === '' || $region === ''
        || $zip_code === '' || $email === '' || $password_raw === '') {
        echo 'Error: All fields are required.';
        mysqli_close($db_connection);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Error: Please enter a valid email address.';
        mysqli_close($db_connection);
        exit;
    }
    if (strlen($password_raw) < 8) {
        echo 'Error: Password must be at least 8 characters.';
        mysqli_close($db_connection);
        exit;
    }

    // Hash the RAW password. (The original escaped it BEFORE hashing, which
    // meant passwords containing quotes/backslashes could never log in.)
    $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

    // Generate a unique verification code
    $verification_code = bin2hex(random_bytes(16));

    // Ensure the 'users' table exists (no user input; safe to run as-is)
    $check_table  = "SHOW TABLES LIKE 'users'";
    $table_result = mysqli_query($db_connection, $check_table);
    if ($table_result && mysqli_num_rows($table_result) == 0) {
        $create_table = "CREATE TABLE users (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(255) NOT NULL,
            full_address VARCHAR(255) NOT NULL,
            country VARCHAR(255) NOT NULL,
            region VARCHAR(255) NOT NULL,
            zip_code VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(255) NOT NULL,
            expiry VARCHAR(255) NOT NULL,
            verification_code VARCHAR(255) NOT NULL,
            verification_status ENUM('unverified', 'verified') NOT NULL
        )";
        mysqli_query($db_connection, $create_table);
    }

    // Check if the email already exists (prepared statement)
    $check = mysqli_prepare($db_connection, 'SELECT id FROM users WHERE email = ? LIMIT 1');
    if (!$check) {
        error_log('Stock platform register: prepare(check) failed: ' . mysqli_error($db_connection));
        mysqli_close($db_connection);
        die('A database error occurred. Please try again later.');
    }
    mysqli_stmt_bind_param($check, 's', $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    $email_exists = mysqli_stmt_num_rows($check) > 0;
    mysqli_stmt_close($check);

    if ($email_exists) {
        echo 'Error: Email already exists.';
        mysqli_close($db_connection);
        exit;
    }

    // Insert the new user (prepared statement)
    $insert = mysqli_prepare(
        $db_connection,
        "INSERT INTO users
            (full_name, full_address, country, region, zip_code, email, password, role, verification_code, verification_status, expiry)
         VALUES (?, ?, ?, ?, ?, ?, ?, 'FREE', ?, 'unverified', 'never')"
    );
    if (!$insert) {
        error_log('Stock platform register: prepare(insert) failed: ' . mysqli_error($db_connection));
        mysqli_close($db_connection);
        die('A database error occurred. Please try again later.');
    }
    mysqli_stmt_bind_param(
        $insert, 'ssssssss',
        $full_name, $full_address, $country, $region, $zip_code, $email, $password_hash, $verification_code
    );

    if (mysqli_stmt_execute($insert)) {
        mysqli_stmt_close($insert);

        // Send verification email
        $subject = 'Email Verification';
        $message = 'Please click the link below to verify your email address:' . "\r\n";
        $message .= 'https://YOUR-DOMAIN-HERE/app/verification.php?code=' . $verification_code;
        $headers = 'From: noreply@YOUR-DOMAIN-HERE' . "\r\n" .
            'Reply-To: noreply@YOUR-DOMAIN-HERE' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        mail($email, $subject, $message, $headers);

        mysqli_close($db_connection);
        header('Location: ../profile.php');
        exit;
    } else {
        // Log the real error; show a generic message (no SQL/DB details leaked)
        error_log('Stock platform register: insert failed: ' . mysqli_stmt_error($insert));
        mysqli_stmt_close($insert);
        echo 'Error: Could not complete registration. Please try again later.';
    }
}
