<?php
// Start the session
session_start();

// Check if the user is logged in and has a valid CSRF token
if (!isset($_SESSION['admin_user']) || !$_SESSION['admin_user'] || !isset($_SESSION['admin_token'])) {
    // The user is not logged in or has an invalid CSRF token, redirect them to the login page
    header('Location: login.php');
    exit;
}

// Check if the logout button has been pressed
if (isset($_POST['logout'])) {
    // Destroy the session
    session_destroy();

    // Redirect the user to the login page
    header('Location: login.php');
    exit;
}
require_once '../../app/db-config.php';
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin Page</title>
	<meta name="description" content="Admin page" />
	<meta name='robots' content='NOINDEX, NOFOLLOW, NOARCHIVE, NOSNIPPET' />
	<link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png">
	<link rel="stylesheet" href="../css/style.css">
	<meta name="theme-color" content="#1b1b1b">	
	<style>.adminButton{margin-right: 10px;}.adminImg{width: 200px!important; height: auto;}</style>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-light bg-light" role="navigation">
	<div class="container">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-controls="bs-example-navbar-collapse-1" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
    <a class="navbar-brand" href="../index.php">TrendTonic</a>
		<div id="bs-example-navbar-collapse-1" class="collapse navbar-collapse">
			<ul id="menu-header" class="nav navbar-nav">
				<li class="nav-item"><a href="../manufacturing.php" class="nav-link">Manufacturing</a></li>
				<li class="nav-item"><a href="../health-care.php" class="nav-link">Health Care</a></li>
				<li class="nav-item"><a href="../real-estate.php" class="nav-link">Real Estate</a></li>
				<li class="nav-item"><a href="../technology.php" class="nav-link">Technology</a></li>
				<li class="nav-item"><a href="../services.php" class="nav-link">Services</a></li>
				<li class="nav-item"><a href="../finance.php" class="nav-link">Finance</a></li>
				<li class="nav-item"><a href="../energy.php" class="nav-link">Energy</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="container">
	<div class="row">
		<!-- Use col-md-8 + col-md-4 for sidebar, use col-md-12 without sidebar -->
		<div class="col-md-8">
		<h1>Platform</h1>

		<!-- Admin Main Page -->
		<div id="main">    
			<h2>Admin home Page</h2>
			<p>Welcome, <?php echo $_SESSION['username']; ?>!</p>
		<div id="progress-bar" style="display:none;">
			<progress value="0" max="100"></progress>
		</div>
		<div id="response"></div>
	</div>

	</div>
	<!-- SIDEBAR CONTENT -->
	<div class="col-md-4">
	<form action="" method="post"><input type="submit" name="logout" value="Logout"></form>
	<br><br>
	<h2>Fetch Stocks:</h2>
	<button id="fetch-all-data-button" class="btn btn-primary">Fetch All Data</button>
	<br><br>
	<h2>Calculate:</h2>
	<button id="calculate-button" class="btn btn-primary">Calculate</button>
	<br><br>
	<h2>Averages:</h2>
	<button id="calculateAvg-button" class="btn btn-primary">Calculate AVG</button>
	</div>
  </div>
</div>
<footer class="bg-light py-3 footer">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="footerWidget">
                	<h3>Economic</h3>
					<button id="realGdp-button" class="btn btn-primary">GDP</button>
					<button id="inflation-button" class="btn btn-primary">Inflation</button>
                </div>
            </div>
			<div class="col-md-3">
                <div class="footerWidget">
					<h3>Heading</h3>
					<p>Description </p>
                </div>
            </div>
			<!-- COUNT NYSE STOCKS -->			
			<div class="col-md-3">
				<div class="footerWidget">
					<h3>NYSE:</h3>
					<p>
					<?php
						// Connect to database
						$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
						// Check connection
						if (!$conn) {
							die("Connection failed: " . mysqli_connect_error());
						}
						// Get the number of stocks in the NYSE table
						$select_data = "SELECT COUNT(*) as count FROM NYSE";
						$result = mysqli_query($conn, $select_data);
						$row = mysqli_fetch_assoc($result);
						// Display the number of stocks
						echo $row['count'];
						// Close database connection
						mysqli_close($conn);
					?>
					</p>
				</div>
			</div>
			<!-- COUNT NASDAQ STOCKS -->	
			<div class="col-md-3">
				<div class="footerWidget">
					<h3>NASDAQ:</h3>
					<p>
					<?php
						// Connect to database
						$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

						// Check connection
						if (!$conn) {
							die("Connection failed: " . mysqli_connect_error());
						}
						// Get the number of stocks in the NASDAQ table
						$select_data = "SELECT COUNT(*) as count FROM NASDAQ";
						$result = mysqli_query($conn, $select_data);
						$row = mysqli_fetch_assoc($result);
						// Display the number of stocks
						echo $row['count'];
						// Close database connection
						mysqli_close($conn);
					?>
					</p>
				</div>
			</div>
        </div>
		<div class="clearfix"></div>
		<div id="footerMenu">
			<div class="row">
				<div class="col-md-12">
					<div class="menu-footer-menu-container">
					<ul id="menu-footer-menu" class="secondary-menu">
						<li class="menu-item"><a href="../index.php">Home Page</a></li>
					</ul>
					</div>   
				</div>
			</div>
		</div>
    </div>
	<div class="clearfix"></div>
    <p class="copyright">Copyright © YOUR-DOMAIN-HERE - All rights receirved.</p>
</footer>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
$(document).ready(function() {
    $("#fetch-all-data-button").click(function() {
		var startTime = new Date().getTime();
        $.ajax({
            url: '../fetch/fetch-all-data.php',
            type: 'post',
            beforeSend: function() {
				var endTime = new Date().getTime();
                var elapsedTime = endTime - startTime;
                var progress = (elapsedTime / 1000) * 100;
                $("progress").val(progress);
                $("#progress-bar").show();
            },
            success: function(response) {
                $("#progress-bar").hide();
                $("#response").html(response);
            }
        });
    });
	$("#calculate-button").click(function() {
		var startTime = new Date().getTime();
        $.ajax({
            url: '../fetch/data-fetching.php',
            type: 'post',
            beforeSend: function() {
                $("#progress-bar").show();
            },
            success: function(response) {
				var endTime = new Date().getTime();
                var elapsedTime = endTime - startTime;
                var progress = (elapsedTime / 1000) * 100;
                $("progress").val(progress);
                $("#progress-bar").hide();
            }
        });
    });
	
		$("#calculateAvg-button").click(function() {
		var startTime = new Date().getTime();
        $.ajax({
            url: '../fetch/sector-averages.php',
            type: 'post',
            beforeSend: function() {
                $("#progress-bar").show();
            },
            success: function(response) {
				var endTime = new Date().getTime();
                var elapsedTime = endTime - startTime;
                var progress = (elapsedTime / 1000) * 100;
                $("progress").val(progress);
                $("#progress-bar").hide();
            }
        });
    });
	
	
	$("#realGdp-button").click(function() {
		var startTime = new Date().getTime();
        $.ajax({
            url: '../fetch/real-gdp.php',
            type: 'post',
            beforeSend: function() {
                $("#progress-bar").show();
            },
            success: function(response) {
				var endTime = new Date().getTime();
                var elapsedTime = endTime - startTime;
                var progress = (elapsedTime / 1000) * 100;
                $("progress").val(progress);
                $("#progress-bar").hide();
            }
        });
    });
	
	$("#inflation-button").click(function() {
		var startTime = new Date().getTime();
        $.ajax({
            url: '../fetch/inflation-rate.php',
            type: 'post',
            beforeSend: function() {
                $("#progress-bar").show();
            },
            success: function(response) {
				var endTime = new Date().getTime();
                var elapsedTime = endTime - startTime;
                var progress = (elapsedTime / 1000) * 100;
                $("progress").val(progress);
                $("#progress-bar").hide();
            }
        });
    });
});
</script>
</body>
</html>