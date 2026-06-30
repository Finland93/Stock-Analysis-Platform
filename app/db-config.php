<?php
/*
 * Database configuration for the Stock Analysis Platform.
 *
 * SETUP:
 *   1. Fill in your real database credentials below.
 *   2. Place this file where the application expects it. Most files load it via
 *      require_once '../../app/db-config.php'  (two levels up from /app),
 *      so keeping it OUTSIDE the public web root is both expected and safer.
 *   3. NEVER commit real credentials to version control.
 *
 * NOTE: a few files use a shorter path ('../app/db-config.php'). If some pages
 * work and others say "config not found", make the include paths consistent or
 * place a copy where each expects it.
 */

$db_host     = 'localhost';
$db_username = 'CHANGE_ME';
$db_password = 'CHANGE_ME';
$db_name     = 'CHANGE_ME';

// Alpha Vantage API key (used by the data fetchers and the client-side
// stock chart). NOTE: js/chart.js echoes this into client JavaScript, so it
// is visible to visitors. For better security, proxy chart requests through
// a server-side endpoint instead of exposing the key.
$av_api_key  = 'CHANGE_ME';

// Use the procedural error style the app relies on (return false on error)
// instead of throwing, so our explicit checks work the same on PHP 8.1+.
if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

// Do not display PHP/database errors to visitors in production. Errors are
// written to the server log instead. Set display_errors to 1 only while
// debugging on a private/staging environment.
@ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

// Load shared helpers (secure_session, db, e, csrf_*). Using __DIR__ makes this
// work no matter which relative path a page used to include this config file.
require_once __DIR__ . '/bootstrap.php';
