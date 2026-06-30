<?php
require_once '../../app/db-config.php';   // shared helpers (secure_session, csrf_verify)
secure_session();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF protection
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        echo 'Invalid or expired request. Please reload the page and try again.';
        exit;
    }

    // Strip CR/LF from any value that goes into a mail header (prevents header injection)
    $strip = function ($v) { return trim(str_replace(array("\r", "\n"), '', (string) $v)); };

    $inquiry = $strip($_POST['inquiry'] ?? '');
    $name    = $strip($_POST['name'] ?? '');
    $email   = $strip($_POST['email'] ?? '');
    $subject = $strip($_POST['subject'] ?? '');
    $body    = trim((string) ($_POST['message'] ?? ''));   // body can keep newlines

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Please enter a valid email address.';
        exit;
    }
    if ($name === '' || $subject === '' || $body === '') {
        echo 'Please fill in all fields.';
        exit;
    }

    // reCAPTCHA (only enforced when a real secret key is configured in db-config.php)
    $secret_key = isset($recaptcha_secret_key) ? $recaptcha_secret_key : '';
    $captcha_ok = true;
    if ($secret_key !== '' && stripos($secret_key, 'HERE') === false && stripos($secret_key, 'ADD') === false) {
        $resp = $_POST['g-recaptcha-response'] ?? '';
        $ip   = isset($_SERVER['REMOTE_ADDR']) ? preg_replace('/[^0-9a-fA-F:.]/', '', $_SERVER['REMOTE_ADDR']) : '';
        $v = @file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secret_key) . '&response=' . urlencode($resp) . '&remoteip=' . urlencode($ip));
        $v = json_decode($v);
        $captcha_ok = !empty($v->success);
    }
    if (!$captcha_ok) {
        echo 'reCAPTCHA verification failed.';
        exit;
    }

    // Recipient is fixed. From is a fixed site address (no spoofing / open relay);
    // the visitor's validated address goes in Reply-To.
    $to           = 'YOUR-OWN-EMAIL-HERE';
    $mail_subject = $subject;
    $message      = "Inquiry: $inquiry\n" .
                    "Name: $name\n" .
                    "Email: $email\n\n" .
                    "Message:\n$body\n";
    $headers      = "From: noreply@YOUR-DOMAIN-HERE\r\n" .
                    "Reply-To: $email\r\n";

    if (mail($to, $mail_subject, $message, $headers)) {
        echo 'Your message was sent successfully.';
    } else {
        echo 'There was a problem sending your message.';
    }
}
