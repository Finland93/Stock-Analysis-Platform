<?php
    require_once '../app/db-config.php';

    $conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
	
    function displaySectorAverages($sector) {
        global $conn;

		$sql = "SELECT exchange, avg_peRatio, avg_pbRatio, avg_rsiAnalysis FROM sorting_data WHERE sector='$sector' LIMIT 2";
		$result = mysqli_query($conn, $sql);

        $html = '<div class="mobileTable">';
        $html .= '<table class="stock-listings-table">';
        $html .= "<tr><th>Sector averages:</th><th>P/E</th><th>P/B</th><th>RSI</th></tr>";
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $html .= "<tr><td>" . $row["exchange"]. "</td><td>" . number_format($row["avg_peRatio"], 2). "</td><td>" . number_format($row["avg_pbRatio"], 2). "</td><td>" . number_format($row["avg_rsiAnalysis"], 2). "</td></tr>";
            }
        } else {
            $html .= "<tr><td colspan='4'>No data found</td></tr>";
        }
        $html .= "</table>";
        $html .= "</div>";

        mysqli_close($conn);
        return $html;
    }
    
?>