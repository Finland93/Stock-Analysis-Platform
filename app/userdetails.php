<?php
session_start();
  // Look for csrf token
if (!isset($_SESSION['user_token'])) {
  die('Invalid user token');
} else {
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Include database configuration file
	require_once '../../app/db-config.php';

    // Connect to the database
    $conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

    // Get the form data
    $full_name = $_POST['full_name'];
    $full_address = $_POST['full_address'];
    $country = $_POST['country'];
    $region = $_POST['region'];
    $zip_code = $_POST['zip_code'];
    $password = $_POST['password'];

    // Fetch the current data from the database
    $stmt = mysqli_prepare($conn, "SELECT full_name, full_address, country, region, zip_code, password FROM users WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $current_full_name, $current_full_address, $current_country, $current_region, $current_zip_code, $current_password);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Compare the current data with the submitted data
    if ($full_name !== $current_full_name || $full_address !== $current_full_address || $country !== $current_country || $region !== $current_region || $zip_code !== $current_zip_code) {
      // Hash the password
      $password = password_hash($password, PASSWORD_DEFAULT);

      // Prepare the SQL statement
      $stmt = mysqli_prepare($conn, "UPDATE users SET full_name=?, full_address=?, country=?, region=?, zip_code=?, password=? WHERE id=?");

      // Bind the parameters to the SQL statement
      mysqli_stmt_bind_param($stmt, "sssssssi", $full_name, $full_address, $country, $region, $zip_code, $password, $_SESSION['user_id']);

      // Execute the SQL statement
      mysqli_stmt_execute($stmt);

      // Close the statement
      mysqli_stmt_close($stmt);
    }

    // Close the connection
    mysqli_close($conn);
	
	// Redirect to the profile page
    header('Location: ../profile.php');
    exit;
  }
}
?>