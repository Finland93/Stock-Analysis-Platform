<?php
// Start the session
session_start();

// Check if the user is logged in and has a valid CSRF token
if (!isset($_SESSION['admin_user']) || !$_SESSION['admin_user'] || !isset($_SESSION['admin_token'])) {
    // The user is not logged in or has an invalid CSRF token, redirect them to the login page
    header('Location: ../admin/login.php');
    exit;
}


require_once '../../app/db-config.php';

// Connect to database
$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

// Check if the table exists
$table_check_sql = "SHOW TABLES LIKE 'inflation_rate'";
$table_check_result = mysqli_query($conn, $table_check_sql);

if (mysqli_num_rows($table_check_result) == 0) {
    // Table does not exist, create the table
    $create_table_sql = "CREATE TABLE inflation_rate (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date DATE,
        value FLOAT
    )";
    mysqli_query($conn, $create_table_sql);
}

// API key
$api_key = "CHANGE-API-KEY-HERE";

// API URL
$url = "https://www.alphavantage.co/query?function=INFLATION&apikey=" . $api_key;

// Retrieve data from API URL
$data = file_get_contents($url);

// Decode the JSON data
$json = json_decode($data, true);

// Get the values from the JSON data
$values = $json["data"];

// Get the last 16 values
$last_32_values = array_slice($values, 0, 32);

// Loop through the last 32 values and insert them into the database
foreach ($last_32_values as $value) {
    $inflation = $value["value"];
    $date = $value["date"];

    // date + value bound as parameters
    if ($check = mysqli_prepare($conn, "SELECT 1 FROM inflation_rate WHERE date = ? LIMIT 1")) {
        mysqli_stmt_bind_param($check, "s", $date);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        $exists = mysqli_stmt_num_rows($check) > 0;
        mysqli_stmt_close($check);

        if (!$exists && ($ins = mysqli_prepare($conn, "INSERT INTO inflation_rate (date, value) VALUES (?, ?)"))) {
            mysqli_stmt_bind_param($ins, "sd", $date, $inflation);
            mysqli_stmt_execute($ins);
            mysqli_stmt_close($ins);
        }
    }
}

// Close the database connection
mysqli_close($conn);