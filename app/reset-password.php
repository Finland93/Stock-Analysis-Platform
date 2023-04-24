<?php

// Include database configuration file
require_once '../../app/db-config.php';

// Connect to database
$db_connection = mysqli_connect($db_host, $db_username, $db_password, $db_name);

if(isset($_POST['reset_password'])){
  // Check if email exists in the database
  $email = mysqli_real_escape_string($db_connection, $_POST['email']);
  $check_email = mysqli_query($db_connection, "SELECT email FROM users WHERE email='$email'");
  if(mysqli_num_rows($check_email) > 0){
    // Generate a random password
    $new_password = generateRandomPassword();
    // Hash the password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    // Update the password in the database
    $update_password = mysqli_query($db_connection, "UPDATE users SET password='$hashed_password' WHERE email='$email'");
    if($update_password){
      // Send the new password to the user's email
      sendPasswordResetEmail($email, $new_password);
      // Show success message
      echo '<div class="alert alert-success">A new password has been sent to your email address.</div>';
    } else {
      // Show error message
      echo  '<div class="alert alert-danger">There was an error resetting your password. Please try again later.</div>';
    }
  } else {
    // Show error message
   echo '<div class="alert alert-danger">Email address not found in our database. Please enter a valid email address.</div>';
  }
}

// Generate a random password
function generateRandomPassword(){
  $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
  $password = array();
  $alpha_length = strlen($alphabet) - 1;
  for ($i = 0; $i < 8; $i++) {
    $n = rand(0, $alpha_length);
    $password[] = $alphabet[$n];
  }
  return implode($password);
}

// Send the password reset email
function sendPasswordResetEmail($email, $new_password){
  $to = $email;
  $subject = 'Password Reset';
  $message = 'Your new password is: ' . $new_password;
  $headers = 'From: noreply@YOUR-DOMAIN-HERE' . "\r\n" .
    'Reply-To: noreply@YOUR-DOMAIN-HERE' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
  mail($to, $subject, $message, $headers);
}

?>