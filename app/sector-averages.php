<?php
require_once '../app/db-config.php';

$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (!$conn) {
    error_log('Stock platform sector-averages(display): DB connection failed: ' . mysqli_connect_error());
    die('A database error occurred. Please try again later.');
}

function displaySectorAverages($sector) {
    global $conn;

    $html  = '<div class="mobileTable">';
    $html .= '<table class="stock-listings-table">';
    $html .= "<tr><th>Sector averages:</th><th>P/E</th><th>P/B</th><th>RSI</th></tr>";

    // Sector bound as a parameter (prevents SQL injection)
    $stmt = mysqli_prepare($conn, "SELECT exchange, avg_peRatio, avg_pbRatio, avg_rsiAnalysis FROM sorting_data WHERE sector = ? LIMIT 2");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $sector);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $html .= "<tr><td>" . htmlspecialchars($row["exchange"], ENT_QUOTES)
                       . "</td><td>" . number_format($row["avg_peRatio"], 2)
                       . "</td><td>" . number_format($row["avg_pbRatio"], 2)
                       . "</td><td>" . number_format($row["avg_rsiAnalysis"], 2)
                       . "</td></tr>";
            }
        } else {
            $html .= "<tr><td colspan='4'>No data found</td></tr>";
        }
        mysqli_stmt_close($stmt);
    } else {
        $html .= "<tr><td colspan='4'>No data found</td></tr>";
    }

    $html .= "</table>";
    $html .= "</div>";
    return $html;
}
?>
