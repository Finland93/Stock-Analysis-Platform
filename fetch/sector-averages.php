<?php
// Start the session
session_start();

// Require an authenticated admin
if (!isset($_SESSION['admin_user']) || !$_SESSION['admin_user'] || !isset($_SESSION['admin_token'])) {
    header('Location: ../admin/login.php');
    exit;
}

require_once '../../app/db-config.php';

// Connect to database
$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (!$conn) {
    error_log('Stock platform sector-averages: DB connection failed: ' . mysqli_connect_error());
    die('A database error occurred. Please try again later.');
}

$exchange_types = array('NYSE', 'NASDAQ');

foreach ($exchange_types as $exchange_type) {
    // Hardcoded table name (only NYSE/NASDAQ; safe to interpolate)
    $table_name = ($exchange_type === 'NYSE') ? 'NYSE' : 'NASDAQ';

    // Get all unique sectors
    $sectors = array();
    if ($res = $conn->query("SELECT DISTINCT sector FROM $table_name")) {
        while ($row = $res->fetch_assoc()) {
            if ($row['sector'] !== null && $row['sector'] !== '') {
                $sectors[] = $row['sector'];
            }
        }
        $res->free();
    }

    // Average values per sector (sector bound as a parameter)
    $sorting_data = array();
    if ($avg_stmt = $conn->prepare(
        "SELECT AVG(peRatio) AS avg_peRatio, AVG(pbRatio) AS avg_pbRatio, AVG(rsiAnalysis) AS avg_rsiAnalysis FROM $table_name WHERE sector = ?"
    )) {
        foreach ($sectors as $sector) {
            $avg_stmt->bind_param('s', $sector);
            $avg_stmt->execute();
            $r = $avg_stmt->get_result();
            if ($r && ($row = $r->fetch_assoc())) {
                $sorting_data[$sector] = array(
                    'avg_peRatio'     => $row['avg_peRatio'],
                    'avg_pbRatio'     => $row['avg_pbRatio'],
                    'avg_rsiAnalysis' => $row['avg_rsiAnalysis'],
                );
            }
        }
        $avg_stmt->close();
    }

    // Upsert each sector's averages (all values bound)
    $check  = $conn->prepare("SELECT exchange FROM sorting_data WHERE sector = ? AND exchange = ? LIMIT 1");
    $update = $conn->prepare("UPDATE sorting_data SET avg_peRatio = ?, avg_pbRatio = ?, avg_rsiAnalysis = ? WHERE sector = ? AND exchange = ?");
    $insert = $conn->prepare("INSERT INTO sorting_data (exchange, sector, avg_peRatio, avg_pbRatio, avg_rsiAnalysis) VALUES (?, ?, ?, ?, ?)");

    if ($check && $update && $insert) {
        foreach ($sorting_data as $sector => $data) {
            $pe  = $data['avg_peRatio'];
            $pb  = $data['avg_pbRatio'];
            $rsi = $data['avg_rsiAnalysis'];

            $check->bind_param('ss', $sector, $exchange_type);
            $check->execute();
            $check->store_result();
            $exists = $check->num_rows > 0;
            $check->free_result();

            if ($exists) {
                $update->bind_param('dddss', $pe, $pb, $rsi, $sector, $exchange_type);
                $update->execute();
            } else {
                $insert->bind_param('ssddd', $exchange_type, $sector, $pe, $pb, $rsi);
                $insert->execute();
            }
        }
        $check->close();
        $update->close();
        $insert->close();
    }
}

$conn->close();
