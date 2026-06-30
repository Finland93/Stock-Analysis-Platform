<?php

// Load config + shared helpers, then start a hardened session.
require_once '../../app/db-config.php';
secure_session();

// Connect to database
$db_connection = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (!$db_connection) {
    error_log('Stock platform verify: DB connection failed: ' . mysqli_connect_error());
    die('A database error occurred. Please try again later.');
}

// Verification codes are 32 hex characters (bin2hex of 16 random bytes)
$verification_code = (string) ($_GET['code'] ?? '');
if ($verification_code === '' || !ctype_xdigit($verification_code) || strlen($verification_code) > 64) {
    die('Invalid verification link.');
}

// Find the matching user (prepared statement)
$select = mysqli_prepare(
    $db_connection,
    'SELECT email, full_name, full_address, country, region, zip_code FROM users WHERE verification_code = ? LIMIT 1'
);
if (!$select) {
    error_log('Stock platform verify: prepare(select) failed: ' . mysqli_error($db_connection));
    die('A database error occurred. Please try again later.');
}
mysqli_stmt_bind_param($select, 's', $verification_code);
mysqli_stmt_execute($select);
$result = mysqli_stmt_get_result($select);
$user   = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($select);

if (!$user) {
    die('Invalid verification link.');
}

// Mark the account as verified (prepared statement)
$update = mysqli_prepare($db_connection, "UPDATE users SET verification_status = 'verified' WHERE verification_code = ?");
if (!$update) {
    error_log('Stock platform verify: prepare(update) failed: ' . mysqli_error($db_connection));
    die('A database error occurred. Please try again later.');
}
mysqli_stmt_bind_param($update, 's', $verification_code);

if (mysqli_stmt_execute($update)) {
    mysqli_stmt_close($update);

    // Send welcome email with the user's details
    $to      = $user['email'];
    $subject = 'Welcome to Trend Tonic';
    $message = 'Dear ' . $user['full_name'] . ", welcome! Here are your account details:\r\n";
    $message .= 'Full Name: ' . $user['full_name'] . "\r\n";
    $message .= 'Full Address: ' . $user['full_address'] . "\r\n";
    $message .= 'Country: ' . $user['country'] . "\r\n";
    $message .= 'Region: ' . $user['region'] . "\r\n";
    $message .= 'Zip Code: ' . $user['zip_code'] . "\r\n";
    $message .= 'Email: ' . $user['email'] . "\r\n";
    $headers = 'From: noreply@YOUR-DOMAIN-HERE' . "\r\n" .
        'Reply-To: noreply@YOUR-DOMAIN-HERE' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, $message, $headers);

    mysqli_close($db_connection);
    header('Location: ../profile.php');
    exit;
} else {
    error_log('Stock platform verify: update failed: ' . mysqli_stmt_error($update));
    mysqli_stmt_close($update);
    mysqli_close($db_connection);
    die('Could not verify your account. Please try again later.');
}
