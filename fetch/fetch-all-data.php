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

// Build API URL
$api_url = 'https://www.alphavantage.co/query?function=LISTING_STATUS&apikey=' . $av_api_key;

// Fetch API data
$csv_data = file_get_contents($api_url);

// Save data to file
file_put_contents('all-data.csv', $csv_data);

// Read CSV file
$rows = array_map('str_getcsv', explode("\n", $csv_data));
$header = array_shift($rows);

// Connect to database
$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

// Check connection
if (!$conn) {
    error_log('Stock platform fetch-all: DB connection failed: ' . mysqli_connect_error());
    die('A database error occurred. Please try again later.');
}

// Function to create a table
function createTable($conn, $table_name) {
    $check_table = "SHOW TABLES LIKE '$table_name'";
    $result = mysqli_query($conn, $check_table);

    if (mysqli_num_rows($result) == 0) {
        $create_table = "CREATE TABLE $table_name (
		id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		symbol VARCHAR(255) NOT NULL,
		name VARCHAR(255) NOT NULL,
		assetType VARCHAR(255) NOT NULL,
		sector VARCHAR(255),
		market_cap FLOAT,
		peRatio FLOAT,
		pbRatio FLOAT,
		eps FLOAT,
		rsiAnalysis FLOAT,
		processed INT(11) NOT NULL
		)";
        
        $result = mysqli_query($conn, $create_table);
        if (!$result) {
            die("Error creating table: " . mysqli_error($conn));
        }
    }
}

// Function to insert data into a table
function insertData($conn, $table_name, $row) {
    $symbol    = $row[0];
    $name      = $row[1];
    $assetType = $row[3];
    $sector    = "Sector";
    $epsNumber = 0;
    $market_cap = 0;
    $peRatio = 0;
    $pbRatio = 0;
    $rsiAnalysis = 0;
    $processed = 0;

    // Check if data already exists ($table_name is a hardcoded NYSE/NASDAQ value)
    $exists = false;
    if ($check = mysqli_prepare($conn, "SELECT 1 FROM $table_name WHERE symbol = ? AND name = ? LIMIT 1")) {
        mysqli_stmt_bind_param($check, "ss", $symbol, $name);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        $exists = mysqli_stmt_num_rows($check) > 0;
        mysqli_stmt_close($check);
    }

    if (!$exists) {
        $sql = "INSERT INTO $table_name (symbol, name, assetType, sector, market_cap, peRatio, pbRatio, eps, rsiAnalysis, processed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($insert = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($insert, "ssssdddddi", $symbol, $name, $assetType, $sector, $market_cap, $peRatio, $pbRatio, $epsNumber, $rsiAnalysis, $processed);
            if (!mysqli_stmt_execute($insert)) {
                error_log("Stock platform fetch-all: insert failed: " . mysqli_stmt_error($insert));
            }
            mysqli_stmt_close($insert);
        }
    }
}

// Function to delete data from a table
function deleteData($conn, $table_name, $rows) {
    $symbols = [];
    foreach ($rows as $row) {
        if ($row[3] == 'Stock') {
            $symbols[] = $row[0];
        }
    }

    // Collect orphaned symbols first (do NOT delete while iterating the result set)
    $to_delete = [];
    if ($res = mysqli_query($conn, "SELECT symbol FROM $table_name")) {
        while ($row = mysqli_fetch_assoc($res)) {
            if (!in_array($row['symbol'], $symbols, true)) {
                $to_delete[] = $row['symbol'];
            }
        }
        mysqli_free_result($res);
    }

    if ($to_delete) {
        if ($del = mysqli_prepare($conn, "DELETE FROM $table_name WHERE symbol = ?")) {
            foreach ($to_delete as $sym) {
                mysqli_stmt_bind_param($del, "s", $sym);
                if (!mysqli_stmt_execute($del)) {
                    error_log("Stock platform fetch-all: delete failed: " . mysqli_stmt_error($del));
                }
            }
            mysqli_stmt_close($del);
        }
    }
}

// Loop through each row
foreach ($rows as $row) {
    // Check if asset type is Stock
    if ($row[3] == 'Stock') {
        // Check exchange type
        if ($row[2] == 'NYSE') {
            $table_name = 'NYSE';
            createTable($conn, $table_name);
            insertData($conn, $table_name, $row);
        } elseif ($row[2] == 'NASDAQ') {
            $table_name = 'NASDAQ';
            createTable($conn, $table_name);
            insertData($conn, $table_name, $row);
        }
    }
}

// Delete data from tables
deleteData($conn, 'NYSE', $rows);
deleteData($conn, 'NASDAQ', $rows);

// Close database connection
mysqli_close($conn);

?>