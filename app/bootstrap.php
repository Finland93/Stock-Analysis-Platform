<?php
/*
 * Shared bootstrap helpers for the Stock Analysis Platform.
 *
 * This file is pulled in automatically by db-config.php, so every page that
 * already includes the config gets these helpers without any extra require.
 * Migrate pages to these helpers over time to remove duplicated boilerplate:
 *
 *   secure_session();            // instead of session_start()
 *   $conn = db();                // instead of mysqli_connect(...) + error check
 *   echo e($value);              // instead of htmlspecialchars(...)
 *   $token = csrf_token();       // get/create the CSRF token for a form
 *   if (csrf_verify($posted))    // verify a submitted CSRF token
 */

if (!function_exists('secure_session')) {
    /**
     * Start a session once, with hardened cookie flags (HttpOnly, SameSite,
     * and Secure when served over HTTPS). Safe to call multiple times.
     */
    function secure_session() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params(array(
                'lifetime' => 0,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
                'secure'   => $secure,
            ));
        } else {
            session_set_cookie_params(0, '/', '', $secure, true);
        }
        session_start();
    }
}

if (!function_exists('db')) {
    /**
     * Return a single shared mysqli connection (created on first use).
     * Dies with a generic message (and logs the real error) on failure.
     */
    function db() {
        static $conn = null;
        if ($conn instanceof mysqli) {
            return $conn;
        }
        global $db_host, $db_username, $db_password, $db_name;
        $conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
        if (!$conn) {
            error_log('Stock platform: DB connection failed: ' . mysqli_connect_error());
            die('A database error occurred. Please try again later.');
        }
        mysqli_set_charset($conn, 'utf8mb4');
        return $conn;
    }
}

if (!function_exists('e')) {
    /** Escape a value for safe output in HTML. */
    function e($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    /** Get (creating if needed) the current session's CSRF token. */
    function csrf_token() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            secure_session();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /** Output a ready-made hidden CSRF input for a form. */
    function csrf_field() {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('csrf_verify')) {
    /** Constant-time check of a submitted CSRF token against the session. */
    function csrf_verify($token) {
        return !empty($_SESSION['csrf_token'])
            && is_string($token)
            && hash_equals($_SESSION['csrf_token'], $token);
    }
}
