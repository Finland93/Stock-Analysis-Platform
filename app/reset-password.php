<?php

// Load config + shared helpers, then start a hardened session.
require_once '../../app/db-config.php';
secure_session();

// Connect to database
$db_connection = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (!$db_connection) {
    error_log('Stock platform reset: DB connection failed: ' . mysqli_connect_error());
    die('A database error occurred. Please try again later.');
}

// The reset form posts button name="submit" and field name="resetEmail".
// (The original checked $_POST['reset_password'] and $_POST['email'], which
// never matched the form, so password reset did nothing at all.)
if (isset($_POST['submit'])) {

    // CSRF protection
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        mysqli_close($db_connection);
        die('Invalid or expired request. Please reload the page and try again.');
    }

    $email = trim((string) ($_POST['resetEmail'] ?? ''));

    // Same response whether or not the email exists (avoids user enumeration)
    $generic = '<div class="alert alert-success">If that email address is registered, a new password has been sent to it.</div>';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<div class="alert alert-danger">Please enter a valid email address.</div>';
        mysqli_close($db_connection);
        exit;
    }

    // Does the email exist? (prepared statement)
    $check = mysqli_prepare($db_connection, 'SELECT id FROM users WHERE email = ? LIMIT 1');
    if (!$check) {
        error_log('Stock platform reset: prepare(check) failed: ' . mysqli_error($db_connection));
        mysqli_close($db_connection);
        die('A database error occurred. Please try again later.');
    }
    mysqli_stmt_bind_param($check, 's', $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    $exists = mysqli_stmt_num_rows($check) > 0;
    mysqli_stmt_close($check);

    if ($exists) {
        $new_password   = generateRandomPassword();
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update = mysqli_prepare($db_connection, 'UPDATE users SET password = ? WHERE email = ?');
        if ($update) {
            mysqli_stmt_bind_param($update, 'ss', $hashed_password, $email);
            if (mysqli_stmt_execute($update)) {
                sendPasswordResetEmail($email, $new_password);
            } else {
                error_log('Stock platform reset: update failed: ' . mysqli_stmt_error($update));
            }
            mysqli_stmt_close($update);
        } else {
            error_log('Stock platform reset: prepare(update) failed: ' . mysqli_error($db_connection));
        }
    }

    // Always the same message
    echo $generic;
    mysqli_close($db_connection);
    exit;
}

// Generate a random password
function generateRandomPassword() {
    $alphabet     = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $password     = '';
    $alpha_length = strlen($alphabet) - 1;
    for ($i = 0; $i < 12; $i++) {
        $password .= $alphabet[random_int(0, $alpha_length)];
    }
    return $password;
}

// Send the password reset email
function sendPasswordResetEmail($email, $new_password) {
    $subject = 'Password Reset';
    $message = 'Your new password is: ' . $new_password;
    $headers = 'From: noreply@YOUR-DOMAIN-HERE' . "\r\n" .
        'Reply-To: noreply@YOUR-DOMAIN-HERE' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    mail($email, $subject, $message, $headers);
}
