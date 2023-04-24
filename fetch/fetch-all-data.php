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
    die("Connection failed: " . mysqli_connect_error());
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
    $symbol = $row[0];
    $name = $row[1];
    $assetType = $row[3];
    $sector = "Sector"; 
	$epsNumber = 0;
	$market_cap = 0;
    $peRatio = 0; 
    $pbRatio = 0; 
    $rsiAnalysis = 0; 
    $processed = 0;

    // Check if data already exists
    $check_data = "SELECT * FROM $table_name WHERE symbol='$symbol' AND name='$name'";
    $result = mysqli_query($conn, $check_data);

    if (mysqli_num_rows($result) == 0) {
        $insert_data = "INSERT INTO $table_name (symbol, name, assetType, sector, market_cap, peRatio, pbRatio, eps, rsiAnalysis, processed) 
		VALUES ('$symbol', '$name', '$assetType', '$sector', '$market_cap', '$peRatio', '$pbRatio', '$epsNumber', '$rsiAnalysis', '$processed')";
             $result = mysqli_query($conn, $insert_data);
        if (!$result) {
            die("Error inserting data: " . mysqli_error($conn));
        }
    }
}

// Function to delete data from a table
function deleteData($conn, $table_name, $rows) {
    $symbols = [];

    // Create an array of symbols in the all-data.csv file
    foreach ($rows as $row) {
        if ($row[3] == 'Stock') {
            array_push($symbols, $row[0]);
        }
    }

    // Check if a symbol exists in the database table but not in the all-data.csv file
    $select_data = "SELECT symbol FROM $table_name";
    $result = mysqli_query($conn, $select_data);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (!in_array($row['symbol'], $symbols)) {
                $delete_data = "DELETE FROM $table_name WHERE symbol='".$row['symbol']."'";
                $result = mysqli_query($conn, $delete_data);
                if (!$result) {
                    die("Error deleting data: " . mysqli_error($conn));
                }
            }
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