<?php
require_once '../app/limit-logins.php';
require_once '../../app/db-config.php';   // DB credentials + shared helpers
secure_session();

$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (!$conn) {
    error_log('Stock platform admin-login: DB connection failed: ' . mysqli_connect_error());
    die('A database error occurred. Please try again later.');
}

$error_message = '';

if (isset($_POST['submit'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid or expired request. Please reload the page and try again.';
    } else {
        // reCAPTCHA v3 (only enforced if a real secret key is configured)
        $secret_key = isset($recaptcha_secret_key) ? $recaptcha_secret_key : '';
        $captcha_ok = true;
        if ($secret_key !== '' && stripos($secret_key, 'HERE') === false) {
            $resp   = $_POST['g-recaptcha-response'] ?? '';
            $verify = @file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secret_key) . '&response=' . urlencode($resp));
            $verify = json_decode($verify, true);
            $captcha_ok = !empty($verify['success']);
        }

        if (!$captcha_ok) {
            $error_message = 'reCAPTCHA verification failed.';
        } else {
            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            if ($username === '' || $password === '') {
                $error_message = 'All fields are required. Please fill out the form.';
            } elseif (checkLoginAttempts($username)) {
                $error_message = 'Too many attempts. Please try again later.';
            } else {
                $stmt = mysqli_prepare($conn, 'SELECT password, role FROM admin_details WHERE username = ? LIMIT 1');
                mysqli_stmt_bind_param($stmt, 's', $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $admin  = $result ? mysqli_fetch_assoc($result) : null;
                mysqli_stmt_close($stmt);

                // Verify the ENTERED password against the STORED hash.
                // (The original hashed the entered password and compared it to
                // itself, so ANY password was accepted - a full auth bypass.)
                if ($admin && password_verify($password, $admin['password']) && $admin['role'] === 'Admin') {
                    session_regenerate_id(true);
                    resetLoginAttempts($username);
                    $_SESSION['admin_user'] = true;
                    $_SESSION['username']   = $username;
                    if (!isset($_SESSION['admin_token'])) {
                        $_SESSION['admin_token'] = bin2hex(random_bytes(32));
                    }
                    mysqli_close($conn);
                    header('Location: index.php');
                    exit;
                } else {
                    updateLoginAttempts($username);
                    $error_message = 'Incorrect username or password.';
                }
            }
        }
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login Admin Area</title>
	<meta name="description" content="Admin login page" />
	<meta name='robots' content='NOINDEX, NOFOLLOW, NOARCHIVE, NOSNIPPET' />
	<link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png">
	<meta name="theme-color" content="#1b1b1b">	
	<link rel="stylesheet" href="../css/style.css">
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
    <div class="col-md-12">
    <h1>TrendTonic - Admin Login</h1>
    <?php if (!empty($error_message)): ?>
    <p style="color: red;"><?php echo e($error_message); ?></p>
    <?php endif; ?>
    <form action="" method="post">
	<label for="username">Username:</label>
	<input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? e($_POST['username']) : ''; ?>">
	 <label for="password">Password:</label>
    <input type="password" id="password" name="password">
	<?php echo csrf_field(); ?>
	<input type="hidden" class="form-control" id="g-recaptcha-response" name="g-recaptcha-response">
    <input type="submit" name="submit" value="Login">
	</form>
		<script src="https://www.google.com/recaptcha/api.js?render=YOURAPIKEY"></script>
			<script>
			grecaptcha.ready(function() {
			grecaptcha.execute("YOURAPIKEY", {action: ""})
				.then(function(token) {
				document.getElementById("g-recaptcha-response").value = token;
				});
			});
			</script>
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
						<li class="menu-item "><a href="../blog/index.php">Blog</a></li>
						<li class="menu-item "><a href="../contact.php">About us</a></li>
						<li class="menu-item"><a href="../terms-conditions.php">Terms & Conditions</a></li>
						<li class="menu-item "><a href="../privacy.php">Privacy Policy</a></li>
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
</script>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>document.addEventListener('contextmenu', event => event.preventDefault());</script>
</body>
</html>