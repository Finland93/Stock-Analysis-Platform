<?php
require_once '../../app/db-config.php';   // config + shared helpers
secure_session();

	if (!isset($_SESSION['user_id'])) {
		die('Not authorized.');
	}
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify($_POST['csrf_token'] ?? '')) {
		die('Invalid or expired request. Please reload the page and try again.');
	}

	// Connect to the database
	$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

	if ($conn->connect_error) {
		error_log('Stock platform delete-profile: DB connection failed: ' . $conn->connect_error);
		die('A database error occurred. Please try again later.');
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