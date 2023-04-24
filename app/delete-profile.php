<?php
session_start();

	// Check if the user is logged in and has a valid session token
	if (!isset($_SESSION['user_token'])) {
		die('Invalid user token');
	}

	// Check if the user ID is set in the session
	if (!isset($_SESSION['user_id'])) {
		die('User ID not found in session');
	}

	// Include database configuration file
	require_once '../../app/db-config.php';

	// Connect to the database
	$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

	// Check the connection
	if ($conn->connect_error) {
		die("Error connecting to the database: " . $conn->connect_error);
	}

	// Get the user's email address
	$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
	$sql = "SELECT email FROM users WHERE id = ?";

	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$user = $result->fetch_assoc();
	$email = $user['email'];

	// Generate a verification code
	$verification_code = mt_rand(100000, 999999);

	// Store the verification code in the database
	$sql = "UPDATE users SET verification_code = ? WHERE id = ?";

	$stmt = $conn->prepare($sql);
	$stmt->bind_param("si", $verification_code, $user_id);
	$stmt->execute();

	// Send an email with the verification code
	$to = $email;
	$subject = "Account Deletion Verification";
	$message = "Click the link below to verify your account deletion: https://YOUR-DOMAIN-HERE/app/verify-deletion.php?user_id=$user_id&code=$verification_code";
	$headers = "From: noreply@YOUR-DOMAIN-HERE" . "\r\n";

	if (mail($to, $subject, $message, $headers)) {
		echo "Verification code sent to your email";
	} else {
		echo "Error sending verification code";
	}

	$stmt->close();
	$conn->close();
	
	// Redirect to the profile page
    header('Location: ../profile.php');
    exit;
?>