<?php session_start(); ?>
<!-- Page to add a new client to the database -->
<html>
<head>
	<link rel="stylesheet" type="text/css" href="Design.css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Add New Client</title>
<head>
<body>
	<?php
		//Files containing PHP general functions and functions to interact with MySQL database
		require_once('Functions/PHPFunc.php');
		require_once('Functions/SQLFunc.php');

		//Determine if session is active
		if(isset($_SESSION['uname'])){
			//Display the navigation bar
			include('NavBar.html');
			//Determine user's access level
			if($_SESSION['level']>1){
				//Display the form to add a new client
				include('NewClientForm.html');
				//Determine if form has been submitted
				if(isset($_POST['submit'])){
					//Determine if all fields have been filled
					if(isset($_POST['client'])){
						$client=check_input($_POST['client']);
						if(addClient($client)){
							echo"<h3> A new client has been successfully added to the database.</h3>";
						}else{
							echo"<h2>An error occured while adding this client.</h2>";
							echo"<p>Please try again or contact your system administrator.</p>";
						}
					}else{
						echo"<h2>Please be sure to fill all fields to properly add a new client.</h2>";
					}
				}
			}else{
				//Notify user of authentication failure
				echo"<h2>You are not authorized to access this feature.</h2>";
				echo"<p>Please contact your system administrator.</p>";
			}
		}else{
			//Display login form
			include('Login.html');
			echo"<h2>Your session has expired due to innactivity</h2>";
			echo"<p>Please enter your credentials below to log in.</p>";
		}
	?>
</body>
</html>