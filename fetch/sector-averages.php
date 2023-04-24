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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$exchange_types = array("NYSE", "NASDAQ");

foreach ($exchange_types as $exchange_type) {
    $table_name = ($exchange_type == "NYSE") ? "NYSE" : "NASDAQ";

	// Get all unique sectors
	$query = "SELECT DISTINCT sector FROM $table_name";
	$result = $conn->query($query);

	$sectors = array();
	while ($row = $result->fetch_assoc()) {
		array_push($sectors, $row['sector']);
	}

// Calculate average values for each sector
$sorting_data = array();
foreach ($sectors as $sector) {
    $query = "SELECT AVG(peRatio) as avg_peRatio, AVG(pbRatio) as avg_pbRatio, AVG(rsiAnalysis) as avg_rsiAnalysis FROM $table_name WHERE sector='$sector'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    $avg_peRatio = $row['avg_peRatio'];
    $avg_pbRatio = $row['avg_pbRatio'];
    $avg_rsiAnalysis = $row['avg_rsiAnalysis'];

    $sorting_data[$sector] = array('avg_peRatio' => $avg_peRatio, 'avg_pbRatio' => $avg_pbRatio, 'avg_rsiAnalysis' => $avg_rsiAnalysis);
}

//Check data if exist
foreach ($sorting_data as $sector => $data) {
	$avg_peRatio = $data['avg_peRatio'];
	$avg_pbRatio = $data['avg_pbRatio'];
	$avg_rsiAnalysis = $data['avg_rsiAnalysis'];

	// Check if the data for the sector and exchange type exists
	$check_query = "SELECT * FROM sorting_data WHERE sector='$sector' AND exchange='$exchange_type'";
	$check_result = $conn->query($check_query);

	if ($check_result->num_rows > 0) {
		// Update data
		$query = "UPDATE sorting_data SET avg_peRatio=$avg_peRatio, avg_pbRatio=$avg_pbRatio, avg_rsiAnalysis=$avg_rsiAnalysis WHERE sector='$sector' AND exchange='$exchange_type'";
		$result = $conn->query($query);
	} else {
		// Insert data
		$query = "INSERT INTO sorting_data (exchange, sector, avg_peRatio, avg_pbRatio, avg_rsiAnalysis) VALUES ('$exchange_type', '$sector', $avg_peRatio, $avg_pbRatio, $avg_rsiAnalysis)";
		$result = $conn->query($query);
			}
		}
	}
$conn->close();

?>