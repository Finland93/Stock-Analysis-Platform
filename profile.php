<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>TrendTonic - Profile</title>
	<meta name="description" content="TrendTonic profile page, register now its free. We also offer premium membership wich grants you way more data." />
<!-- Twitter sharing: -->
<meta name="robots" content="index, follow">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@TrendTonicInc">
<meta name="twitter:title" content="TrendTonic - Profile">
<meta name="twitter:description" content="TrendTonic profile page, register now its free. We also offer premium membership wich grants you way more data.">
<meta name="twitter:image" content="img/">

<!-- Facebook Sharing: -->
<meta property="og:url" content="javascript:window.location.href">
<meta property="og:type" content="webpage" />
<meta property="og:title" content="TrendTonic - Profile">
<meta property="og:description" content="TrendTonic profile page, register now its free. We also offer premium membership wich grants you way more data.">
<meta property="og:image" content="img/">
	
	<link rel="stylesheet" href="css/style.css">
	<meta name="theme-color" content="#1b1b1b">	
	<link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
	<link href="https://www.googletagmanager.com" rel="preconnect">
	<link href="www.googletagmanager.com" rel="dns-prefetch">

	<script async src="https://www.googletagmanager.com/gtag/js?id=G-FNP6DCCXWV"></script>
	<script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', 'G-FNP6DCCXWV');</script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-light bg-light" role="navigation">
	<div class="container">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-controls="bs-example-navbar-collapse-1" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
    <a class="navbar-brand" href="https://trend-tonic.com/">TrendTonic</a>
        
		<div id="bs-example-navbar-collapse-1" class="collapse navbar-collapse">
			<ul id="menu-header" class="nav navbar-nav">
				<li class="nav-item"><a href="consumer-discretionary.php" class="nav-link">Cons. Discretionary</a></li>
				<li class="nav-item"><a href="consumer-staples.php" class="nav-link">Cons. Staples</a></li>
				<li class="nav-item"><a href="communication-services.php" class="nav-link">Communication</a></li>
				<li class="nav-item"><a href="health-care.php" class="nav-link">Health Care</a></li>
				<li class="nav-item"><a href="industrials.php" class="nav-link">Industrials</a></li>
				<li class="nav-item"><a href="real-estate.php" class="nav-link">Real Estate</a></li>
				<li class="nav-item"><a href="financials.php" class="nav-link">Financials</a></li>
				<li class="nav-item"><a href="materials.php" class="nav-link">Materials</a></li>
				<li class="nav-item"><a href="utilities.php" class="nav-link">Utilities</a></li>
				<li class="nav-item"><a href="energy.php" class="nav-link">Energy</a></li>
				<li class="nav-item"><a href="information-technology.php" class="nav-link">IT</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="container">
  <div class="row">
  <!-- Use col-md-8 + col-md-4 for sidebar, use col-md-12 without sidebar -->
    <div class="col-md-8">
    <h1>Profile</h1>
	
<div class="message">
    <?php echo (isset($message) ? $message : ''); ?>
</div>
	<br>
<!-- Buttons for forms --> 
    <button type="button" class="btn btn-primary" id="registerBtn">Register</button>
    <button type="button" class="btn btn-primary" id="loginBtn">Login</button>
	
 <!-- Register form --> 	
	<div class="disableRegister" style="display: none;">
  <form action="app/register.php" method="post" id="registerForm" >
    <div class="form-group">
      <label for="full_name">Full Name:</label>
      <input type="text" class="form-control" id="full_name" name="full_name" required>
    </div>
    <div class="form-group">
      <label for="full_address">Full Address:</label>
      <input type="text" class="form-control" id="full_address" name="full_address" required>
    </div>
    <div class="form-group">
      <label for="country">Country:</label>
      <input type="text" class="form-control" id="country" name="country" required>
    </div>
    <div class="form-group">
      <label for="region">Region:</label>
      <input type="text" class="form-control" id="region" name="region" required>
    </div>
    <div class="form-group">
      <label for="zip_code">Zip Code:</label>
      <input type="text" class="form-control" id="zip_code" name="zip_code" required>
    </div>
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="form-group">
      <label for="password">Password:</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <input type="submit" name="submit" value="Submit">
  </form>
</div>

 <!-- Login form --> 
  <div class="disableLogin" style="display: none;">
  <form action="app/login.php" method="post" id="loginForm">
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="form-group">
      <label for="password">Password:</label>
      <input type="password" class="form-control" id="password" name="password" required>
	  <input type="hidden" name="user_token" value="<?php echo $_SESSION['user_token']; ?>">
    </div>
    <input type="submit" name="submit" value="Submit">
    <a href="#" id="lostPassword">Lost your password?</a>
  </form>
</div>

<!-- Lost Password -->
<div class="disableLostPassword" style="display: none;">
  <form action="app/reset-password.php" method="post" id="resetPasswordForm">
      <label for="resetEmail">Email:</label>
      <input type="email" class="form-control" id="resetEmail" name="resetEmail">
    <input type="submit" name="submit" value="Reset Password">
  </form>
</div>
<!-- Logged in user Details -->
<?php 
	if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
	if (!isset($_SESSION['user_token'])) {
    die('Invalid user token');
	} 
	else {
		$user_id = $_SESSION['user_id'];
		require_once '../app/db-config.php';
		$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
		$sql = "SELECT * FROM users WHERE id = '$user_id'";
		$result = mysqli_query($conn, $sql);
		$user = mysqli_fetch_assoc($result);
		$html = '<p><b>Welcome: </b>'  . $user['full_name'] . '</p>';
		$html .= '<p><b>Email: </b>' . $user['email'] . '</p>';
		$html .= '<p><b>Adress: </b>' . $user['full_address'] . '</p>';
		$html .= '<p><b>Country: </b>' . $user['country'] . '</p>';
		$html .= '<p><b>Region: </b>' . $user['region'] . '</p>';
		$html .= '<p><b>Zip code: </b>' . $user['zip_code'] . '</p>';
		$html .= '<p><b>Account: </b>' . $user['role'] . '</p>';
		$html .= '<p><b>Expiry date: </b>' . $user['expiry'] . '</p>';
		$html .= '<form action="" method="post"><input type="submit" name="logout" value="Logout"></form>';
		$html .= '<br>';
		$html .= '<button type="button" class="btn btn-primary" id="editDetails">Edit details</button>';
		echo $html;
	}
	// Check if the logout button has been pressed
	if (isset($_POST['logout'])) {
		// Destroy the session
		session_destroy();
		// Refresh the page
		echo '<script>location.reload();</script>';
		exit;
	}
} ?>
	<!-- Edit User Details -->
	<form action="app/userdetails.php" id="detailsForm" method="post" style="display: none;">
		<div>
			<label for="full_name">Full Name:</label>
			<input type="text" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>">
		</div>
		<div>
			<label for="full_address">Full Address:</label>
			<input type="text" id="full_address" name="full_address" value="<?php echo $user['full_address']; ?>">
		</div>
		<div>
			<label for="country">Country:</label>
			<input type="text" id="country" name="country" value="<?php echo $user['country']; ?>">
		</div>
		<div>
			<label for="region">Region:</label>
			<input type="text" id="region" name="region" value="<?php echo $user['region']; ?>">
		</div>
		<div>
			<label for="zip_code">Zip Code:</label>
			<input type="text" id="zip_code" name="zip_code" value="<?php echo $user['zip_code']; ?>">
		</div>
			<p>If you want to change your password you can change it here, to save above details you must enter your current password 2 times here:</p>
		<div>
			<label for="password">Password:</label>
			<input type="password" id="password" name="password">
		</div>
		<div>
			<label for="password_confirm">Confirm Password:</label>
			<input type="password" id="password_confirm" name="password_confirm">
		</div>
		<div>
			<input type="submit" value="Submit">
		</div>
	</form>

	</div>
		<!-- Sidebar Content --> 
		<div class="col-md-4">
			<h2>Register</h2>
			<p>Registering details here</p>

			<h3>Recurring PRO</h3>
			<p>PRO account monthly fee here</p>

			<h3>One-time PRO</h3>
			<p>PRO Account one-time fee here</p>

			<!-- Delete Account --> 
			<div id="removeAccount" style="display: none;">
				<h3>Delete Account</h3>
				<form action="app/delete-profile.php" method="post"><input class="btn btn-danger" type="submit" name="delAccount" value="Delete Account"></form>
			</div>
		</div>
	<!-- END OF PAGE -->
</div>
</div>

<footer class="bg-light py-3 footer">
    <div class="container">
        <div class="row">
			<div class="warning-container">
				<b>DISCLAIMER HEADING HERE</b>
				<div class="warning-text">
				DISCLAIMER TEXT HERE
				</div>
			</div>		
            <div class="col-md-3">
                <div class="footerWidget">
					<h3>FOOTER HEADING</h3>
					<p>FOOTER TEXT</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="footerWidget">
					<h3>FOOTER HEADING</h3>
					<p>FOOTER TEXT</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="footerWidget">
					<h3>FOOTER HEADING</h3>
					<p>FOOTER TEXT</p>
                </div>
            </div>
            <div class="col-md-3">
                <<div class="footerWidget">
					<h3>FOOTER HEADING</h3>
					<p>FOOTER TEXT</p>
                </div>
            </div>
        </div>
		<div class="clearfix"></div>
		<div id="footerMenu">
			<div class="row">
				<div class="col-md-12">
					<div class="menu-footer-menu-container">
					<ul id="menu-footer-menu" class="secondary-menu">
						<li class="menu-item"><a href="profile.php">Profile</a></li>
						<li class="menu-item"><a href="terms-conditions.php">Terms & Conditions</a></li>
						<li class="menu-item "><a href="privacy.php">Privacy Policy</a></li>
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
$("#registerBtn").click(function() {
showRegisterForm();
});

$("#loginBtn").click(function() {
showLoginForm();
});

$("#lostPassword").click(function() {
showLostpasswordForm();
});

$("#editDetails").click(function() {
editDetailsForm();
});

    function showRegisterForm() {
	var registerForm = document.querySelector(".disableRegister");
	if 	(registerForm.style.display === "none") {
		registerForm.style.display = "block";
		}	 else {
		registerForm.style.display = "none";
		}
		document.querySelector(".disableLogin").style.display = "none";
		document.querySelector(".disableLostPassword").style.display = "none";
	}
  
    function showLoginForm() {
	var logInForm = document.querySelector(".disableLogin");
	if 	(logInForm.style.display === "none") {
		logInForm.style.display = "block";
		}	 else {
		logInForm.style.display = "none";
		}
		document.querySelector(".disableRegister").style.display = "none";
		document.querySelector(".disableLostPassword").style.display = "none";
	}
  
  
    function showLostpasswordForm() {
	var lostPwd = document.querySelector(".disableLostPassword");
	if 	(lostPwd.style.display === "none") {
		lostPwd.style.display = "block";
		}	 
   else {
		lostPwd.style.display = "none";
		}
		document.querySelector(".disableLogin").style.display = "none";
	}
  
 var isLoggedIn = <?php echo (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) ? 'true' : 'false'; ?>;

  if (isLoggedIn) {
    document.getElementById("registerBtn").style.display = "none";
    document.getElementById("loginBtn").style.display = "none";
	document.getElementById("removeAccount").style.display = "block";
	
	document.getElementById("detailsForm").addEventListener("submit", function(event) {
    var password = document.getElementById("password").value;
    var passwordConfirm = document.getElementById("password_confirm").value;

    if (password !== passwordConfirm) {
      alert("The passwords do not match, please try again.");
      event.preventDefault();
    }
	  
  });
  
	function editDetailsForm() {
	var form = document.getElementById("detailsForm");
	if 	(form.style.display === "none") {
		form.style.display = "block";}	
		else {
		form.style.display = "none";}
		}
	}
  
  const warningContainer = document.querySelector('.warning-container');
  const warningText = document.querySelector('.warning-text');

  warningContainer.addEventListener('click', function() {
    if (warningText.style.display === 'none') {
      warningText.style.display = 'block';
    } else {
      warningText.style.display = 'none';
    }
  });
  //PRO User theme color change
  var proUser = <?php  echo (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['role'] == 'PRO') ? 'true' : 'false'; ?>;
  if (proUser) {
    document.documentElement.style.setProperty("--highlight", "var(--pro-highlight)");
  }
</script>
<!-- DISABLE RIGHT CLICK -->
<script>document.addEventListener('contextmenu', event => event.preventDefault());</script>
</body>
</html>