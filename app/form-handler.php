<?php
// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get form data
  $inquiry = $_POST['inquiry'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $subject = "Subject: " . $_POST['subject'];
  $message = "Inquiry: $inquiry\n" .
             "Name: $name\n" .
             "Email: $email\n" .
             "Message: $message\n";
  $recaptcha_response = $_POST['g-recaptcha-response'];

  // Your reCAPTCHA secret key
  $secret_key = 'ADD HERE';

  // Verify reCAPTCHA response
  $recaptcha = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$recaptcha_response&remoteip=" . $_SERVER['REMOTE_ADDR']);
  $recaptcha = json_decode($recaptcha);

  if ($recaptcha->success) {
    // Recipient email address
    $to = 'YOUR OWN EMAIL HERE';

    // Email headers
    $headers = "From: $email\r\n" .
               "Reply-To: $email\r\n";

    // Send the email
    if (mail($to, $subject, $message, $headers)) {
      echo 'Your message was sent successfully.';
    } else {
      echo 'There was a problem sending your message.';
    }
  } else {
    echo 'reCAPTCHA verification failed.';
  }
}

?>