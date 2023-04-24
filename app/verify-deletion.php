<?php

// Include database configuration file
require_once '../../app/db-config.php';

// Connect to the database
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

	// Check the connection
	if ($conn->connect_error) {
		die("Error connecting to the database: " . $conn->connect_error);
	}

	// Check if the user ID and verification code are provided
	if (!isset($_GET["user_id"]) || !isset($_GET["code"])) {
		die("User ID and verification code not provided");
	}

	// Get the user ID and verification code from the query string
	$user_id = mysqli_real_escape_string($conn, $_GET["user_id"]);
	$verification_code = mysqli_real_escape_string($conn, $_GET["code"]);

	// Verify the verification code
	$sql = "SELECT verification_code FROM users WHERE id = ?";

	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$user = $result->fetch_assoc();

	if ($user['verification_code'] != $verification_code) {
		die("Invalid verification code");
	}

	// Delete the user's account
	$sql = "DELETE FROM users WHERE id = ?";

	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $user_id);
	$stmt->execute();

	if ($stmt->affected_rows > 0) {
	echo "User account deleted successfully";
	} else {
		echo "Error deleting user account: " . $stmt->error;
	}

	$stmt->close();
	$conn->close();

	// Destroy the session
    session_destroy();

	// Redirect to the profile page
	header('Location: ../profile.php');
	exit;

?>