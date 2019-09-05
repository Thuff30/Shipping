<?php
	session_start();
?>
<!-- Page to allow the addition of new user profiles -->
<html>
<head>
	<link rel="stylesheet" type="text/css" href="Design.css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Add New User</title>
</head>
<body>
	<?php
	
		require_once('Functions/PHPFunc.php');
		require_once('Functions/UserFunc.php');
		//Determine if session is active
		if(isset($_SESSION['uname'])){
			include('NavBar.html');
			//Determine user's access level
			if($_SESSION['level']>1){
				include('NewUserForm.html');
				//Determine if form has been submitted
				if(isset($_POST['submit'])){
					//Determine if all fields have been filled
					if(isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['pass1']) && isset($_POST['pass2']) && isset($_POST['level'])){
						//Verify password matches confirm password field
						if($_POST['pass1']==$_POST['pass2']){
							$fname=check_input($_POST['fname']);
							$lname=check_input($_POST['lname']);
							$finit=substr($fname,0,1);
							$uname=$finit.$lname;
							$pass=check_input($_POST['pass1']);
							$level=$_POST['level'];
							//Determine successful addition of user
							if(addUser($uname, $pass, $level)==true){
	?>
	<h3> A new user with the user ID <?=$uname ?> has been successfully added to the database.</h3>
	<?php
							}else{
	?>
	<h2>An error occured while adding this user.</h2>
	<p>Please try again or contact your system administrator.</p>
	<?php
							}
						}else{
	?>
	<h2>Please enter the same password twice to confirm it.</h2>
	<?php
						}
					}else{
	?>
	<h2>Please be sure to fill all fields to properly create a new user account</h2>
	<?php
					}
				}
			}else{
	?>
	<h2>You are not authorized to access this feature.</h2>
	<p>Please contact your system administrator</p>
	<?php
			}
		}else{
			include('Login.html');
	?>
	<h2>Your session has expired due to innactivity</h2>
	<p>Please enter your credentials below to log in.</p>
	<?php
		}
	?>
</body>
</html>