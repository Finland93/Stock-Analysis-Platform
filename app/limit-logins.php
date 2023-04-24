<?php
// Define the maximum number of allowed login attempts
define('MAX_LOGIN_ATTEMPTS', 5);

// Define the time period (in seconds) during which login attempts are limited
define('LOGIN_ATTEMPT_LIMIT_PERIOD', 30 * 60);

// A function to check if the user has exceeded the maximum number of login attempts
function checkLoginAttempts($username) {
    // Get the current time
    $now = time();

    // Get the number of login attempts for the user
    if (isset($_SESSION['login_attempts'][$username])) {
        $attempts = $_SESSION['login_attempts'][$username]['attempts'];
        $last_attempt = $_SESSION['login_attempts'][$username]['last_attempt'];
    } else {
        $attempts = 0;
        $last_attempt = 0;
    }

    // Check if the user has exceeded the maximum number of login attempts
    if ($attempts >= MAX_LOGIN_ATTEMPTS && ($now - $last_attempt) < LOGIN_ATTEMPT_LIMIT_PERIOD) {
        return true;
    } else {
        return false;
    }
}

// A function to update the login attempts for the user
function updateLoginAttempts($username) {
    // Get the current time
    $now = time();

    // Update the number of login attempts for the user
    if (isset($_SESSION['login_attempts'][$username])) {
        $_SESSION['login_attempts'][$username]['attempts']++;
        $_SESSION['login_attempts'][$username]['last_attempt'] = $now;
    } else {
        $_SESSION['login_attempts'][$username] = array(
            'attempts' => 1,
            'last_attempt' => $now
        );
    }
}

// A function to reset the login attempts for the user
function resetLoginAttempts($username) {
    unset($_SESSION['login_attempts'][$username]);
}