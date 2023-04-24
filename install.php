<?php
session_start();
?>

<h2>Step 1: Database Configuration</h2>
<?php if (isset($error_message)): ?>
<p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form action="" method="post">
    <label for="db_host">Database Host:</label>
    <input type="text" id="db_host" name="db_host" value="<?php echo isset($_POST['db_host']) ? htmlspecialchars($_POST['db_host']) : ''; ?>">

    <label for="db_name">Database Name:</label>
    <input type="text" id="db_name" name="db_name" value="<?php echo isset($_POST['db_name']) ? htmlspecialchars($_POST['db_name']) : ''; ?>">

    <label for="db_username">Database Username:</label>
    <input type="text" id="db_username" name="db_username" value="<?php echo isset($_POST['db_username']) ? htmlspecialchars($_POST['db_username']) : ''; ?>">

    <label for="db_password">Database Password:</label>
    <input type="password" id="db_password" name="db_password" value="<?php echo isset($_POST['db_password']) ? htmlspecialchars($_POST['db_password']) : ''; ?>">

    <label for="av_api_key">Alphavantage API Key:</label>
    <input type="text" id="av_api_key" name="av_api_key" value="<?php echo isset($_POST['av_api_key']) ? htmlspecialchars($_POST['av_api_key']) : ''; ?>">

    <label for="recaptcha_site_key">Recaptcha Site Key:</label>
    <input type="text" id="recaptcha_site_key" name="recaptcha_site_key" value="<?php echo isset($_POST['recaptcha_site_key']) ? htmlspecialchars($_POST['recaptcha_site_key']) : ''; ?>">

    <label for="recaptcha_secret_key">Recaptcha Secret Key:</label>
	 <input type="text" id="recaptcha_secret_key" name="recaptcha_secret_key" value="<?php echo isset($_POST['recaptcha_secret_key']) ? htmlspecialchars($_POST['recaptcha_secret_key']) : ''; ?>">

    <input type="submit" name="submit_step1" value="Proceed to Step 2">
</form>

<?php

// Check if the form has been submitted
if (isset($_POST['submit_step1'])) {

    // Retrieve form data
    $host = htmlspecialchars($_POST['db_host']);
    $name = htmlspecialchars($_POST['db_name']);
    $username = htmlspecialchars($_POST['db_username']);
    $password = htmlspecialchars($_POST['db_password']);
    $av_api_key = htmlspecialchars($_POST['av_api_key']);
    $recaptcha_site_key = htmlspecialchars($_POST['recaptcha_site_key']);
    $recaptcha_secret_key = htmlspecialchars($_POST['recaptcha_secret_key']);

    // Validate form data
    if (!$host || !$name || !$username || !$password || !$av_api_key || !$recaptcha_site_key || !$recaptcha_secret_key) {
        $error_message = "All fields are required. Please fill out the form.";
    } else {
		//Make directory for APP
		if (!is_dir('../app')) {
		mkdir('../app');
		}
		
        // Write the configuration to a file
        $config_file = "<?php\n";
        $config_file .= "\$db_host = '$host';\n";
        $config_file .= "\$db_name = '$name';\n";
        $config_file .= "\$db_username = '$username';\n";
        $config_file .= "\$db_password = '$password';\n";
        $config_file .= "\$av_api_key = '$av_api_key';\n";
        $config_file .= "\$recaptcha_site_key = '$recaptcha_site_key';\n";
        $config_file .= "\$recaptcha_secret_key = '$recaptcha_secret_key';\n";
        $config_file .= "?>";
		
        //Saving our config file outside web root
        file_put_contents('../app/db-config.php', $config_file);
		
        // Proceed to Step 2
        header('Location: install.php?step=2');
        exit;
    }
}

// Step 2: Admin Account Creation ?>

<?php if (isset($_GET['step']) && $_GET['step'] == 2): ?>

<h2>Step 2: Admin Account Creation</h2>

<?php if (isset($error_message)): ?>
<p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form action="" method="post">
    <label for="username">Username:</label>
	<input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>">

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

    <input type="submit" name="submit_step2" value="Create Admin Account">
</form>

<?php endif; ?>

// Check if the form has been submitted
<?php if (isset($_POST['submit_step2'])) {

    // Connect to the database
    include '../app/db-config.php';
    $conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
    if (!$conn) {
        $error_message = "Error connecting to the database: " . mysqli_connect_error();
    } else {
        // Create the admin-details table
        $table_sql = "CREATE TABLE admin_details (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(30) NOT NULL,
            password VARCHAR(150) NOT NULL,
            email VARCHAR(50) NOT NULL,
            role VARCHAR(30) NOT NULL
        )";
        mysqli_query($conn, $table_sql);

        // Retrieve form data
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
        $email = htmlspecialchars($_POST['email']);
        $role = "Admin";
		
        // Validate form data
        if (!$username || !$password || !$email) {
            $error_message = "All fields are required. Please fill out the form.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert the admin account into the database
            $insert_sql = "INSERT INTO admin_details (username, password, email, role)
                VALUES ('$username', '$hashed_password', '$email', '$role')";
            mysqli_query($conn, $insert_sql);

            // Send an email with the admin account details
            $to = $email;
            $subject = "Admin Account - Stock Analysis";
            $message = "Here is your admin account details:\n\nUsername: $username\nPassword: $password";
            $headers = "From: noreply@trend-tonic.com";
            
            mail($to, $subject, $message, $headers);
			//Create averages table
			$sector = "CREATE TABLE sorting_data (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                sector VARCHAR(255) NOT NULL,
                exchange VARCHAR(255) NOT NULL,
                avg_peRatio FLOAT NOT NULL,
                avg_pbRatio FLOAT NOT NULL,
                avg_rsiAnalysis FLOAT NOT NULL
			)";
			mysqli_query($conn, $sector);
		
			//Create gdp table
			$gdp = "CREATE TABLE gdp_data (
				date DATE NOT NULL,
				value DECIMAL(10,2) NOT NULL
			)";
			mysqli_query($conn, $gdp);
		
			//Create inflation table
			$inflation = "CREATE TABLE inflation_rate (
				id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				date DATE NOT NULL,
				value DECIMAL(5,2) NOT NULL
			)";
			mysqli_query($conn, $inflation);

            // Redirect to the admin page
            header('Location: admin/index.php');
            exit;
        }

        // Close the database connection
        mysqli_close($conn);
    }
} ?>