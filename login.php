<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include("./dbconnection/connection.php");
$pageName = "SignIn";
?>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>MK Scholars - Login</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">

</head>
<?php
include("./php/login.php");
?>
<style>
	* {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
		font-family: 'Inter', sans-serif;
	}

	body {
		display: flex;
		flex-direction: column;
		justify-content: center;
		background: #f5f5f5;
		width: 100%;
	}

	.auth-container {
		background: white;
		padding: 40px;
		border-radius: 20px;
		box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
		width: 400px;
		transition: 0.3s ease;
	}

	.form-header {
		text-align: center;
		margin-bottom: 40px;
	}

	.form-header h1 {
		color: #2d3436;
		font-size: 28px;
		margin-bottom: 8px;
	}

	.form-header p {
		color: #636e72;
		font-size: 14px;
	}

	.auth-form {
		display: flex;
		flex-direction: column;
		gap: 25px;
	}

	.input-group {
		position: relative;
	}

	.input-group input {
		width: 100%;
		padding: 14px;
		border: 1px solid #e0e0e0;
		border-radius: 8px;
		font-size: 14px;
		transition: 0.3s ease;
	}

	.input-group input:focus {
		outline: none;
		border-color: #74b9ff;
		box-shadow: 0 0 0 3px rgba(116, 185, 255, 0.1);
	}

	.input-group label {
		position: absolute;
		left: 14px;
		top: 14px;
		color: #636e72;
		font-size: 14px;
		pointer-events: none;
		transition: 0.3s ease;
		background: white;
		padding: 0 5px;
	}

	.input-group input:focus+label,
	.input-group input:not(:placeholder-shown)+label {
		top: -10px;
		font-size: 12px;
		color: #74b9ff;
	}

	.submit-btn {
		background: #74b9ff;
		color: white;
		padding: 14px;
		border: none;
		border-radius: 8px;
		font-weight: 500;
		cursor: pointer;
		transition: 0.3s ease;
	}

	.submit-btn:hover {
		background: #4da8ff;
	}

	.switch-form {
		text-align: center;
		margin-top: 20px;
	}

	.switch-form a {
		color: #74b9ff;
		text-decoration: none;
		font-size: 14px;
		transition: 0.3s ease;
	}

	.switch-form a:hover {
		color: #4da8ff;
	}

	.hidden {
		display: none;
	}

	.password-rules {
		font-size: 12px;
		color: #636e72;
		margin-top: 5px;
	}

	.logo {
		/* background-color: #2d3436; */
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.container {
		max-width: 1200px;
		margin: 2rem auto;
		display: flex;
		flex-direction: column;
	}

	.alert {
		padding: 10px;
		margin: 5px 0;
		border-radius: 10px;
		font-size: 12px;
	}

	.alert-danger {
		border: .5px solid #c41f10;
		background-color: #fcd5d2;
	}

	.alert-success {
		border: .5px solid #325737;
		background-color: #cffad4;
	}
</style>


<body>
	<?php include("./partials/coursesNav.php") ?>
	<div class="container">
		<div class="auth-container" id="login-container">
			<div class="logo"><a href="index"><img src="images/logo/logoRound.png" width="100" height="100" alt=""></a></div>

			<div class="form-header">
				<h1>Login Account</h1>
				<p>Get started to premium sections</p>
			</div>
			<div class="<?php echo $class ?>">
				<?php echo $msg ?>
			</div>
			<form class="auth-form" method="post" id="login-form">
				<div class="input-group">
					<input type="text" name="username" value="<?php echo $NoUserName; ?>" id="login-email" placeholder=" ">
					<label for="login-email">Email or Phone</label>
				</div>
				<div class="input-group">
					<input type="password" name="password" id="login-email" placeholder=" ">
					<label for="login-email">Password</label>
				</div>

				<button type="submit" name="login" class="submit-btn">Login</button>
			</form>

			<div class="switch-form">
				Need an account? <a href="./sign-up">Sign Up</a><br>
				<a href="./forgot-password">Forgot Password?</a>
			</div>
		</div>
	</div>

</body>

</html>