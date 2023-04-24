<?php

function getGdpData() {
    require_once '../app/db-config.php';

    // Connect to database
    $conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

    // Get the data from the database
    $gdp_data_sql = "SELECT * FROM gdp_data ORDER BY date";
    $gdp_data_result = mysqli_query($conn, $gdp_data_sql);

    $gdp_chart_data = [];
    while ($row = mysqli_fetch_assoc($gdp_data_result)) {
	$gdp_chart_data[] = [
		'x' => $row['date'],
		'y' => $row['value']
	];
	}
	// Close the database connection
	mysqli_close($conn);
	return $gdp_chart_data;
}

function generateGdpChartHtml() {
    $gdp_chart_data = getGdpData();
    ?>
	
    <div class="col-md-12">
        <canvas id="gdp-chart"></canvas>
    </div>
    <div class="clearfix"></div>
	<script>
    var gdp_chart_data = <?php echo json_encode($gdp_chart_data); ?>;
	var gdpData = {
    labels: gdp_chart_data.map(function(d) { return d.x; }),
    datasets: [{
        label: 'Gross Domestic Product USA',
        data: gdp_chart_data.map(function(d) { return d.y; }),
        backgroundColor: 'rgb(0, 123, 255)',
        borderWidth: 1
    }]
	};
	var gdpCtx = document.getElementById('gdp-chart').getContext('2d');
	var gdpChart = new Chart(gdpCtx, {
		type: 'line',
		data: gdpData,
		options: {
			scales: {
				yAxes: [{
					ticks: {
						min: 3000,
						beginAtZero: false
					},
					scaleLabel: {
						display: true,
						labelString: 'Billions of dollars'
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
    </script>
    <?php
}