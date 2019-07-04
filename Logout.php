<?php
	session_start();
?>
<html>
<head>
</head>
<body>
	<?php
		require_once('PHPFunc.php');
		
		logout($_SESSION['uname']);
		include('Login.html');
	?>
</body>
</html>