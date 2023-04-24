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

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


// Fetch NYSE data until all items are processed
while (true) {
  fetchData($conn, "NYSE");
  $sql = "SELECT COUNT(*) as count FROM NYSE WHERE processed = 0";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);

  if ($row['count'] == 0) {
    break;
  }

  // Sleep for 1.67 seconds to achieve 36 fetches per minute
  usleep(1666670);
}

// Fetch NASDAQ data until all items are processed
while (true) {
  fetchData($conn, "NASDAQ");
  $sql = "SELECT COUNT(*) as count FROM NASDAQ WHERE processed = 0";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);

  if ($row['count'] == 0) {
    // Update processed to 0 on both tables
    $sql = "UPDATE NYSE SET processed = 0";
    mysqli_query($conn, $sql);
    $sql = "UPDATE NASDAQ SET processed = 0";
    mysqli_query($conn, $sql);
    break;
  }

  // Sleep for 1.67 seconds to achieve 36 fetches per minute
  usleep(1666670);
}

function fetchData($conn, $table) {
	//Api key
	$api_key = "CHANGE-API-KEY-HERE";
    // Retrieve the first unprocessed symbol
    $sql = "SELECT symbol FROM " . $table . " WHERE processed = 0 LIMIT 1";
    $result = mysqli_query($conn, $sql);

    // Fetch the symbol
    if ($row = mysqli_fetch_assoc($result)) {
        $symbol = $row['symbol'];

        // Build API URL
        $overview_url = "https://www.alphavantage.co/query?function=OVERVIEW&symbol=" . $symbol . "&apikey=" . $api_key;

        // Make API call
        $overview_data = file_get_contents($overview_url);

        // Decode the JSON data
        $overview_data = json_decode($overview_data, true);
		
		$MarketCap = $overview_data["MarketCapitalization"];
		$sql = "UPDATE " . $table . " SET market_cap = '" . $MarketCap . "' WHERE symbol = '" . $symbol . "'";
		mysqli_query($conn, $sql);
        $Sector = $overview_data["Sector"];
		$sql = "UPDATE " . $table . " SET sector = '" . $Sector . "', processed = 1 WHERE symbol = '" . $symbol . "'";
		mysqli_query($conn, $sql);
		$PERatio = $overview_data["PERatio"];
		$sql = "UPDATE " . $table . " SET peRatio = '" . $PERatio . "' WHERE symbol = '" . $symbol . "'";
		mysqli_query($conn, $sql);
		$pbRatio = $overview_data["PriceToBookRatio"];
		$sql = "UPDATE " . $table . " SET pbRatio = '" . $pbRatio . "' WHERE symbol = '" . $symbol . "'";
		mysqli_query($conn, $sql);
		$epsNumber = $overview_data["EPS"];
		$sql = "UPDATE " . $table . " SET eps = '" . $epsNumber . "' WHERE symbol = '" . $symbol . "'";
		mysqli_query($conn, $sql);
		
        // Define the RSI Analyze URL
		$rsi_analyze_url = "https://www.alphavantage.co/query?function=RSI&symbol=" . $symbol . "&interval=daily&time_period=14&series_type=open&apikey=" . $api_key;

		// Get the RSI data from url
		$rsi_data = @file_get_contents(str_replace('{symbol}', $symbol, $rsi_analyze_url));

		// Fetch latest RSI value and save it as rsi variable
		$rsi_data = json_decode($rsi_data, true);
		$rsi_data = $rsi_data["Technical Analysis: RSI"];
		$latest_date = array_keys($rsi_data)[0];
		$rsi = $rsi_data[$latest_date]["RSI"];

		// Store the RSI data
		$sql = "UPDATE " . $table . " SET rsiAnalysis = '" . $rsi . "' WHERE symbol = '" . $symbol . "'";
		mysqli_query($conn, $sql);  
		
    }
	
}

// Close the connection
mysqli_close($conn);

?>