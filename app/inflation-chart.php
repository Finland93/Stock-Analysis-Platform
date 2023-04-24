<?php 
function getInflationData() {
    require_once '../app/db-config.php';
    // Connect to database
    $conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
    // Get the data from the database
    $inflation_data_sql = "SELECT * FROM inflation_rate ORDER BY date";
    $inflation_data_result = mysqli_query($conn, $inflation_data_sql);

    $inflation_chart_data = [];
    while ($row = mysqli_fetch_assoc($inflation_data_result)) {
        $inflation_chart_data[] = [
            'x' => $row['date'],
            'y' => $row['value']
        ];
    }
    // Close the database connection
    mysqli_close($conn);
    return $inflation_chart_data;
}

function generateInflationChartHtml() {
    $inflation_chart_data = getInflationData();
    ?>
	
    <div class="col-md-12">
        <canvas id="inflation-chart"></canvas>
    </div>
    <div class="clearfix"></div>
	<script>
	(function() {
    var inflation_chart_data = <?php echo json_encode($inflation_chart_data); ?>;
	var inflationData = {
    labels: inflation_chart_data.map(function(d) { return d.x; }),
    datasets: [{
        label: 'Inflation Rate USA',
        data: inflation_chart_data.map(function(d) { return d.y; }),
        backgroundColor: 'rgb(0, 123, 255)',
        borderWidth: 1
    }]
	};
    var inflationCtx = document.getElementById('inflation-chart').getContext('2d');
    var inflationChart  = new Chart(inflationCtx, {
        type: 'bar',
        data: inflationData,
        options: {
            scales: {
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Percentage'
                    }
                }],
                xAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Date'
                    }
                }]
            }
        }
    });
})();

</script>

    <?php
}