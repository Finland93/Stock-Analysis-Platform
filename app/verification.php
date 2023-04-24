<?php

session_start();

// Include database configuration file
require_once '../../app/db-config.php';

// Connect to database
$db_connection = mysqli_connect($db_host, $db_username, $db_password, $db_name);

// Check if connection was successful
if (!$db_connection) {
    die("Connection to database failed: " . mysqli_connect_error());
}

// Get verification code from URL
$verification_code = mysqli_real_escape_string($db_connection, $_GET['code']);

// Get user details with the given verification code
$select_query = "SELECT * FROM users WHERE verification_code = '$verification_code'";
$result = mysqli_query($db_connection, $select_query);
$user = mysqli_fetch_assoc($result);

// Update verification status of the user with the given verification code
$update_query = "UPDATE users SET verification_status = 'verified' WHERE verification_code = '$verification_code'";

if (mysqli_query($db_connection, $update_query)) {
    // Send email with user's details
    $to = $user['email'];
    $subject = 'Welcome to Trend Tonic';
    $message = 'Dear ' . $user['full_name'] . ', welcome to YOUR-PLATFORM-NAME-HERE! Here are your account details:';
    $message .= 'Full Name: ' . $user['full_name'] . '\n';
    $message .= 'Full Address: ' . $user['full_address'] . '\n';
    $message .= 'Country: ' . $user['country'] . '\n';
    $message .= 'Region: ' . $user['region'] . '\n';
    $message .= 'Zip Code: ' . $user['zip_code'] . '\n';
    $message .= 'Email: ' . $user['email'] . '\n';
    $headers = 'From: noreply@YOUR-DOMAIN-HERE' . "\r\n" .
        'Reply-To: noreply@YOUR-DOMAIN-HERE' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, $message, $headers);

    // Redirect to success page
    header('Location: ../profile.php');
    exit;
} else {
    echo "Error: " . $update_query . "<br>" . mysqli_error($db_connection);
}

?>