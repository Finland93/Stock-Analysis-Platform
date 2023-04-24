<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>YOUR HEADING</title>
	<meta name="description" content="YOUR DESCRIPTION" />
	<!-- Twitter sharing: -->
	<meta name="robots" content="index, follow">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@YOUR-TWITTER-HERE">
	<meta name="twitter:title" content="YOUR HEADING">
	<meta name="twitter:description" content="YOUR DESCRIPTION">
	<meta name="twitter:image" content="img/YOUR-SOCIAL-SHARE-IMAGE-HERE">

	<!-- Facebook Sharing: -->
	<meta property="og:url" content="javascript:window.location.href">
	<meta property="og:type" content="webpage" />
	<meta property="og:title" content="YOUR HEADING">
	<meta property="og:description" content="YOUR DESCRIPTION">
	<meta property="og:image" content="img/YOUR-SOCIAL-SHARE-IMAGE-HERE">
	
	<!-- FAVICON -->
	<meta name="theme-color" content="#1b1b1b">	
	<link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
	
	<!-- STYLES -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
	<link rel="stylesheet" href="css/style.css">
	
	<!-- GOOGLE ANALYTICS HERE -->
	
</head>
<body>
<nav class="navbar navbar-expand-md navbar-light bg-light" role="navigation">
	<div class="container">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-controls="bs-example-navbar-collapse-1" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
    <a class="navbar-brand" href="index.php">YOUR PLATFORM NAME</a>     
		<div id="bs-example-navbar-collapse-1" class="collapse navbar-collapse">
			<ul id="menu-header" class="nav navbar-nav">
				<li class="nav-item"><a href="manufacturing.php" class="nav-link">Manufacturing</a></li>
				<li class="nav-item"><a href="health-care.php" class="nav-link">Health Care</a></li>
				<li class="nav-item"><a href="real-estate.php" class="nav-link">Real Estate</a></li>
				<li class="nav-item"><a href="technology.php" class="nav-link">Technology</a></li>
				<li class="nav-item"><a href="services.php" class="nav-link">Services</a></li>
				<li class="nav-item"><a href="finance.php" class="nav-link">Finance</a></li>
				<li class="nav-item"><a href="energy.php" class="nav-link">Energy</a></li>
				<li class="nav-item"><a href="economics.php" class="nav-link">Economics USA</a></li>
				<li class="nav-item "><a href="blog/index.php" class="nav-link">Blog</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="container">
	<div class="row">
	<!-- Use col-md-8 + col-md-4 for sidebar, use col-md-12 without sidebar -->
		<div class="col-md-8">
		<h1>Contact us</h1>
		<p>You can contact us with form below. </p>
		<form id="contact-form" action="app/form-handler.php" method="post">
		<div>
        <label for="inquiry">Inquiry:</label>
        <select id="inquiry" name="inquiry">
          <option value="general">General</option>
          <option value="Billing">Billing</option>
          <option value="Account">Account</option>
        </select>
      </div>
      <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
      </div>
      <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div>
        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" required>
      </div>
      <div>
        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea>
      </div>
      <input type="submit" value="Submit">
    </form>
		</div>
		<div class="col-md-4">
			<h2 class="sidebarHeading">Social media</h2>
			<a href="WRITE-HERE-YOUR-TWITTER-PROFILE-URL" title="Follow us on Twitter"><i class="fab fa-twitter socialIcon"></i></a>
			<a href="WRITE-HERE-YOUR-INSTAGRAM-PROFILE-URL" title="Follow us on Instagram"><i class="fab fa-instagram socialIcon"></i></a>
			<a href="WRITE-HERE-YOUR-FACEBOOK-PROFILE-URL" title="Follow us on Facebook"><i class="fab fa-facebook-f socialIcon"></i></a>
			<a href="WRITE-HERE-YOUR-TIKTOK-PROFILE-URL" title="Follow us on TikTok"><i class="fab fa-tiktok socialIcon"></i></a>
		<!-- END OF PAGE -->
		</div>
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

<script>
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
<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<!-- DISABLE RIGHT CLICK -->
<script>document.addEventListener('contextmenu', event => event.preventDefault());</script>
</body>
</html>