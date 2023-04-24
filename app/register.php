<?php

if (isset($_SESSION['user_id'])) {
    header('Location: ../profile.php');
    exit;
}

// Include database configuration file
require_once '../../app/db-config.php';

// Connect to database
$db_connection = mysqli_connect($db_host, $db_username, $db_password, $db_name);

// Check if connection was successful
if (!$db_connection) {
    die("Connection to database failed: " . mysqli_connect_error());
}

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Get form data
    $full_name = mysqli_real_escape_string($db_connection, $_POST['full_name']);
    $full_address = mysqli_real_escape_string($db_connection, $_POST['full_address']);
    $country = mysqli_real_escape_string($db_connection, $_POST['country']);
    $region = mysqli_real_escape_string($db_connection, $_POST['region']);
    $zip_code = mysqli_real_escape_string($db_connection, $_POST['zip_code']);
    $email = mysqli_real_escape_string($db_connection, $_POST['email']);
    $password = mysqli_real_escape_string($db_connection, $_POST['password']);
    $role = 'FREE';
	$expiry  = 'never';

    // Hash password for security
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Generate a unique verification code
    $verification_code = bin2hex(random_bytes(16));
	
	// Check if the table 'users' exists
	$check_table = "SHOW TABLES LIKE 'users'";
	$table_result = mysqli_query($db_connection, $check_table);
	if (mysqli_num_rows($table_result) == 0) {
	// Create the table 'users' if it does not exist
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

	// Check if the email already exists in the table 'users'
	$check_email = "SELECT * FROM users WHERE email = '$email'";
	$email_result = mysqli_query($db_connection, $check_email);
	if (mysqli_num_rows($email_result) > 0) {
	// Show an error message if the email already exists
	echo "Error: Email already exists.";
	exit;
}

    // Insert user data into 'users' table
    $insert_query = "INSERT INTO users (full_name, full_address, country, region, zip_code, email, password, role, verification_code, verification_status,expiry )
                     VALUES ('$full_name', '$full_address', '$country', '$region', '$zip_code', '$email', '$password', '$role', '$verification_code', 'unverified', '$expiry' )";

    if (mysqli_query($db_connection, $insert_query)) {
        // Send verification email
        $to = $email;
        $subject = 'Email Verification';
        $message = 'Please click the link below to verify your email address:';
        $message .= 'https://YOUR-DOMAIN-HERE/app/verification.php?code=' . $verification_code;
        $headers = 'From: noreply@YOUR-DOMAIN-HERE' . "\r\n" .
            'Reply-To: noreply@YOUR-DOMAIN-HERE' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        mail($to, $subject, $message, $headers);
        // Redirect to success page
        header('Location: ../profile.php');
        exit;
    } else {
        echo "Error: " . $insert_query . "<br>" . mysqli_error($db_connection);
    }
}

?>