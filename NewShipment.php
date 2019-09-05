<?php
	session_start();
?>
<!-- Page to create a new shipment and update the MySQL database -->
<html>
<head>
	<link rel="stylesheet" type="text/css" href="Design.css">
	<title>Add New Shipment</title>
</head>
<body>
	<?php
		//File containing all SQL related PHP functions
		require_once('Functions/SQLFunc.php');

		//Determine if user is logged in
		if(isset($_SESSION['uname'])){
			include('NavBar.html');
			//Check user access level
			if($_SESSION['level']<2){
				//Display error message if not high enough
				echo"<h2>You do not have the appropriate access level for this function</h2>
					<p>Please contact your system administrator if you believe this is in error.</p>";
			}else{
				//Page containing form to add a shipment
				include('AddShipForm.php');

				//Determine if form has been filled out
				if(isset($_POST['Submit'])){
					if($_POST['client'] && $_POST['carier'] && $_POST['shipdate'] && $_POST['status'] && $_POST['items'] && $_POST['notes'] && $_POST['tracknum']){
					$client= check_input($_POST['client']);
					$carrier = check_input($_POST['carrier']);
					$shipdate = check_input($_POST['shipdate']);
					$status = $_POST['status'];
					$items = check_input($_POST['items']);
					$notes = check_input($_POST['notes']);
					$tracknum = check_input($_POST['tracknum']);
					
						//Call to function to insert data into database
						if(insertShipment($client, $carrier, $items, $shipdate, $deliverydate, $tracknum, $status)){
							echo "<h1>Shipment successfully added to database</h1>";
						}else{
							echo"<h2>An error occured adding this shipment</h2>
									<p>Please try again or contact your system administrator</p>";
						}
					}else{
						echo"<h2>Please fill all fields</h2>";
					}
				}
			}
		}else{
			//Display form to log in user
			include('Login.html');
			echo"<h2>Your session has expired due to innactivity</h2>";
			echo"<p>Please enter your credentials below to log in.</p>";
		}
	?>
</body>
</html>