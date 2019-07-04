<?php
	require_once('SQLFunc.php');
	
	$host= 'localhost';
	
	//function to add a new user
	function addUser($uname, $pass, $level){
		//variable to show sucess
		$success=false;
		$pass=password_hash($pass, PASSWORD_DEFAULT);
		//Create queries to add user
		$select="SELECT * FROM alpineshipping.Users WHERE Username='".$uname."';";
		$newUser="CREATE USER '".$uname."'@'".$host."' IDENTIFIED BY '".$pass."';";
		$usertable="INSERT INTO alpineshipping.Users VALUES ('".$uname."','".$pass."','".$level."');";
		
		//establish connection
		$mysqli=connectdb();
		if($check=$mysqli->query($select)){
			$count=mysqli_num_rows($check);
			if($count==0){
				$results=$mysqli->query($newUser);
				//prepare statement to determine user access
				switch($level){
					case '1':
						$access = "GRANT SELECT on alpineshipping.* to '".$uname."'@'".$host."';";
						break;
					case '2':
						$access="GRANT SELECT, INSERT, UPDATE on alpineshipping.* to '".$uname."'@'".$host."';";
						break;
					case '3':
						$access="GRANT ALL on alpineshipping.* to '".$uname."'@'".$host."';";
						break;
				}
				//run query to assign privileges
				$privileges=$mysqli->query($access);
				//determine success
				if($tableup=$mysqli->query($usertable)){
					$success=true;
				}
			}
		}
		//close all queries and connections
		$mysqli->close();
		
		return $success;
	}
	
	//function to change user password
	function passChange($uname, $pass, $newpass){
		//Set variable to determine success
		$success=false;
		//create query to alter user
		$haspass= password_hash($newpass, PASSWORD_DEFAULT);
		$alterUser="ALTER USER '".$uname."'@'".$host."' IDENTIFIED BY '".$newpass."';";
		$passconf = "SELECT * FROM alpineshipping.Users WHERE Username='".$uname."';";
		$usertable="UPDATE alpineshipping.Users SET Password='".$haspass."' WHERE Username='".$uname."';";
		//establish connection
		$mysqli=connectdb();
		//run queries
		if($check=$mysqli->query($passconf)){
			$passcheck = password_verify($pass, $row['Password']);
			if($passcheck==true){
				if($results=$mysqli->query($usertable)){
					$lines=$results->rows_affected;
						if($lines==1){
							$success=true;
						}
				}
			}
		}
		$mysqli->query($alterUser);
		//close all queries and connections
		$mysqli->close();
		return $success;
	}
	
	//function to delete users
	function deleteUser($uname){
		//Prepare queries to remove user and revoke privileges
		$privileges="REVOKE ALL on alpineshipping.* FROM '".$uname."'@'".$host."';";
		$dropUser="DROP USER '".$uname."'@'".$host."';";
		//establish connection
		$mysqli=connectdb();
		//run privileges query
		$resultP=$mysqli->query($privileges);
		$lines=$resultP->rows_affected;
		if($lines==1){
			echo "Privileges revoked";
			$finalresults=$mysqli->query($dropUser);
			$finalLines=$finalresults->rows_affected;
			if($finalLines==1){
				echo "User deleted";
			}else{
				echo "User not deleted";
			}
		}else{
			echo "Privileges intact";
		}
	}
?>