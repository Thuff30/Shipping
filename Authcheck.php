<html>
<head>
	<link rel="stylesheet" type="text/css" href="Alpine.css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
	<?php

		//Require all files needed
		require_once('PHPFunc.php');
		require_once('SQLFunc.php');
		
		//Sanitize user input
		$uname = check_input($_POST['user']);
		$pass = check_input($_POST['pass']);
		
		$count = userAuth($uname,$pass);	

		if ($count==0)
		{
		 // Show the login form again.
		 include('Login.html');
	?>
		</br></br>
		<h3>Login Error</h3>
		<h2>The username and password provided do not match any current account.</h2>
		<p>Try again, or contact the system administrator.</p>
		
	<?php	 
		}else{
			$level=verifyLevel($uname);
			//Start session
			login($uname,$pass,$level);
			//include Shipment Search
			include('IndexShipment.php');
		}
		?> 	
</body>
</html>