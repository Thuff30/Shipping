<?php session_start(); ?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="Alpine.css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Add New Client</title>
<head>
<body>
	<?php
		require_once('PHPFunc.php');
		require_once('SQLFunc.php');
		//Determine if session is active
		if(isset($_SESSION['uname'])){
			include('NavBar.html');
			//Determine user's access level
			if($_SESSION['level']>1){
				include('NewClientForm.html');
				//Determine if form has been submitted
				if(isset($_POST['submit'])){
					//Determine if all fields have been filled
					if(isset($_POST['client'])){
						$client=check_input($_POST['client']);
						if(addClient($client)){
							//display success message
							echo"<h3> A new client has been successfully added to the database.</h3>";
						}else{
							//display general failure message
							echo"<h2>An error occured while adding this client.</h2>";
							echo"<p>Please try again or contact your system administrator.</p>";
						}
					}else{
						//Display all fields not filled message
						echo"<h2>Please be sure to fill all fields to properly add a new client.</h2>";
					}
				}
			}else{
				//display user auth message
				echo"<h2>You are not authorized to access this feature.</h2>";
				echo"<p>Please contact your system administrator.</p>";
			}
		}else{
			//display login screen
			include('Login.html');
			echo"<h2>Your session has expired due to innactivity</h2>";
			echo"<p>Please enter your credentials below to log in.</p>";
		}
	?>
</body>
</html>