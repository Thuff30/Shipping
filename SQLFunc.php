<?php
	/*This file contains PHP functions for the application to interact with the MySQL database.
	Each function establishes a connection to the database and interacts using predefined querries*/

	//File with general php functions
	require_once('PHPFunc.php');
	
	//Establish global variables
	static $host = 'localhost';
	static $db = 'alpineshipping';
	
	
	//Function for general login
	function connectdb(){      		
		//Get the DB Parameters
		$mydbparms = getDbparms();
		$success = false;
		//Try to connect to database
		$mysqli = new mysqli($mydbparms->getHost(), $mydbparms->getUsername(), 
	                        $mydbparms->getPassword(),$mydbparms->getDb());
	
		if ($mysqli->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') '
	            . $mysqli->connect_error);      
		}else{
		}
		return $mysqli;
	}
	
	//Function to retrieve preset db login info
	function getDbparms(){
	 	$trimmed = file('dbparms.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$key = array();
		$vals = array();
		foreach($trimmed as $line){
			$pairs = explode("=",$line);    
			$key[] = $pairs[0];
			$vals[] = $pairs[1]; 
		}
		//Combine key and values into an array
		$mypairs = array_combine($key,$vals);
	
		//Assign values to Parameters Class
		$myDbparms = new DbparmsClass($mypairs['username'],$mypairs['password'],
	                $mypairs['host'],$mypairs['db']);
	
		//Display the Paramters values
		return $myDbparms;
	}

	//Class to construct database parameters with getters & setters
	class DBparmsClass{
		
	    //Property declarations  
	    private $username="";
	    private $password="";
	    private $host="";
	    private $db="";
	   
	    //Constructor
	    public function __construct($myusername,$mypassword,$myhost,$mydb){
			$this->username = $myusername;
			$this->password = $mypassword;
			$this->host = $myhost;
			$this->db = $mydb;
	    }
	    
	    //Getter methods 
		  public function getUsername (){
	    	return $this->username;
	    } 
		  public function getPassword (){
	    	return $this->password;
	    } 
		  public function getHost (){
	    	return $this->host;
	    } 
		  public function getDb (){
	    	return $this->db;
	    } 	 
	
	    //Setter methods 
	    public function setUsername ($myusername){
	    	$this->username = $myusername;    	
	    }
	    public function setPassword ($mypassword){
	    	$this->password = $mypassword;    	
	    }
	    public function setHost ($myhost){
	    	$this->host = $myhost;    	
	    }
	    public function setDb ($mydb){
	    	$this->db = $mydb;    	
	    }    
	}
	
	//Function to connect individual users
	function indconnectdb($uname, $pass){
		$mysqli= new mysqli($host, $uname, $pass, $db);
		
		if ($mysqli->connect_error){
			die(Header('Location: FailedLogin.php'));
		}
		return $mysqli;
	}
	
	//Function to enter a new shipment 
	function insertShipment($client, $carrier, $items, $shipdate, $deliverydate, $tracknum, $status){
		
		//Establish variables
		$success=false;
		$date = date_create();
		$dateNow = date_format($date,"Y-m-d"); 		
		
		// Connect to the database
		$mysqli = connectdb();
		
	    //Find clientID and carrierID based on information submitted using function from PHPFunc   
		$clientID = findClient($client);
		$carrierID = findCarrier($carrier);
		
		$insertQuery = "INSERT INTO alpineShipping.Shipment (ClientID, CarrierID, ItemsShipping, EstShipDate, EstDelivery, TrackingNum, Status, DateEntered) 
				VALUES ('".$clientID."', '".$carrierID."', '".$items."', '".$shipdate."', '".$deliverydate."', '".$tracknum."', '".$status."', '".$dateNow."');";
		
		//Fetch the results of query and determine successful execution
		$mysqli->query($insertQuery);
		$result=$mysqli->affected_rows;
		if($thirdresult>0){
			$success=true;
		}

		//Close query and connection
		$result->close();
		$mysqli->close();
		return $success;
	}

	//Function to add new client to the database
	function addClient($business){
		//Establish varaibles
		$success=false;

		//Prepare querry statements
		$select="SELECT * FROM alpineshipping.Client WHERE BusinessName='".$business."';";
		$query="INSERT INTO alpineshipping.Client (BusinessName) VALUES ('".$business."');";
		
		//Establish connection
		$mysqli=connectdb();
		
		//Check for duplicate entries in databases
		if($check=$mysqli->query($select)){
			$count=mysqli_num_rows($check);
			if($count==0){
				//Insert new client entry
				if($result=$mysqli->query($query)){
				$success=true;
				}
			}
		}
		
		//Close query and connection
		$mysqli->close();
		return $success;
	}
	
	//Function to return a list of shipments based on user search criteria
	function searchShipments($limit, $order, $client, $carrier, $startdate, $enddate, $status, $tracknum){
	
		//Establish variables
		$count=0;

		//Prepare main select statement
		$select = "SELECT Shipment.ShipmentID, Client.ClientID, Client.BusinessName, Shipment.ItemsShipping, Shipment.EstDelivery, Shipment.Status, Carrier.CarrierID,
			Carrier.CarrierName, Shipment.TrackingNum, Shipment.Notes, Shipment.DateEntered FROM ((Shipment INNER JOIN Client ON Client.ClientID=Shipment.ClientID)
			JOIN Carrier ON Carrier.CarrierID=Shipment.CarrierID) ";

		//Determine which fields have been filled on form
		if($client){
			//Detemine clientID using function from PHPFunc
			$clientID = findClient($client);
			$select= $select. "WHERE Shipment.ClientID='".$clientID."' ";
			$count++;
		}
		if($carrier){
			//Determine carrierID  using function from PHPFunc
			$carrierID = findCarrier($carrier);
			//Determine if previous additions were made to the query
			if($count>0){
				$select= $select. "AND Shipment.CarrierID='".$carrierID."' ";
				$count++;
			}else{
				$select = $select. "WHERE Shipment.CarrierID='".$carrierID."' ";
				$count++;
			}
		}
		if($startdate){
			if($count>0){
				$select = $select. "AND Shipment.DateEntered > '".$startDate."' ";
				$count++;
			}else{
				$select = $select. "WHERE Shipment.DateEntered > '".$startDate."' ";
				$count++;
			}
		}
		if($enddate){
			if($count>0){
				$select = $select. "AND Shipment.DateEntered < '".$endDate."' ";
				$count++;
			}else{
				$select = $select. "WHERE Shipment.DateEntered < '".$endDate."' ";
				$count++;
			}
		}
		if($status){
			if($count>0){
				$select = $select. "AND Shipment.Status='".$status."' ";
				$count++;
			}else{
				$select = $select. "WHERE Shipment.Status='".$status."' ";
				$count++;
			}
		}
		if($tracknum){
			if($count>0){
				$select = $select. "AND Shipment.TrackingNum='".$tracknum."' ";
			}else{
				$select = $select. "WHERE Shipment.TrackingNum='".$tracknum."' ";
			}
		}
		//Assign value to limit
		$select = $select. "ORDER BY DateEntered ".$order." LIMIT ".$limit.";";
		
		//Establish connection
		$mysqli=connectdb();
		//Retrieve search results
		if($result=$mysqli->query($select)){
			while($row=$result->fetch_assoc()){
				$listShipments[]=$row['ShipmentID'];
			}
		}
		
		//Close query and connection
		$mysqli->close();
		return $listShipments;
	}
	
	//Function to display search results
	function viewShipments($shipID){
		
		//Clear values for $allShipments
		$allShipments="";
		//Establish select query
		$select = "SELECT Shipment.ShipmentID, Client.ClientID, Client.BusinessName, Shipment.ItemsShipping, Shipment.EstDelivery, Shipment.Status, Carrier.CarrierID,
				Carrier.CarrierName, Shipment.TrackingNum, Shipment.Notes, Shipment.DateEntered FROM ((Shipment INNER JOIN Client ON Client.ClientID=Shipment.ClientID)
				JOIN Carrier ON Carrier.CarrierID=Shipment.CarrierID) WHERE ShipmentID = '".$shipID."';";
				
		//Establish connection		
		$mysqli=connectdb();
		
		//Execute select query and store results in an array
		if($result=$mysqli->query($select)){     
			while($row = $result->fetch_assoc()){
				$shipmentID=$row['ShipmentID'];
				$clientID=$row['ClientID'];
				$client=$row['BusinessName'];
				$items=$row['ItemsShipping'];
				$estdel=$row['EstDelivery'];
				$status=$row['Status'];
				$carrierID=$row['CarrierID'];
				$carrier=$row['CarrierName'];
				$tracknum=$row['TrackingNum'];
				$notes=$row['Notes'];
				$entered=$row['DateEnetered'];
				
				//Create an object for reference
				$allShipments= new ShipmentClass($shipmentID, $clientID, $client, $items, $estdel, $status, $carrierID, $carrier, $tracknum, $notes, $entered);
			}
		}
		//Close querry and connection
		$mysqli->close();
		return $allShipments;
	}
	
	//Function to populate client dropdown
	function clientDropdown(){
		//Establish Query
		$clientQuery= "SELECT BusinessName FROM Client;";
		
		//Establish connection and perform query
		$mysqli=connectdb();
		$results=$mysqli->query($clientQuery);
		
		//Populate options to dropdown
		while($row=$results->fetch_assoc()){
			echo "<option value='" .$row['BusinessName']. "'>" .$row['BusinessName']. "</option>";
		}
		//Close query and connection
		$results->close();
		$mysqli->close();
	}
	
	//Function to populate carrier dropdown
	function carrierDropdown(){
		//Establish query
		$carrierQuery= "SELECT CarrierName FROM Carrier;";
		
		//Establish connectionand run query
		$mysqli=connectdb();
		$results=$mysqli->query($carrierQuery);
		
		//Populate options to dropdown
		while($row=$results->fetch_assoc()){
			echo "<option value='" . $row['CarrierName'] . "'>" .$row['CarrierName'] ."</option>";
		}
		//Close query and connection
		$results->close();
		$mysqli->close();
	}
	
	//Function to determine clientID
	function findClient($business){
		
		//Prepare initial query
		$selectQuery = "SELECT ClientID FROM Client WHERE BusinessName='".$business."';";
		
		//establish connection and perform query
		$mysqli=connectdb();
		$results=$mysqli->query($selectQuery);
		while($row = $results->fetch_assoc()){
			$clientID=$row['ClientID'];
		}
		//Close query and connection
		$mysqli->close();
		return $clientID;
	}
	
	//Function to determine carrierID
	function findCarrier($carrier){
		
		//Prepare query to determine carrierID
		$selectQuery = "SELECT CarrierID FROM Carrier WHERE CarrierName='".$carrier."';";
		
		//Establish connection and perform query
		$mysqli=connectdb();
		$results=$mysqli->query($selectQuery);
		while($row=$results->fetch_assoc()){
			$carrierID=$row['CarrierID'];
		}
		//Close query and connection
		$results->close();
		return $carrierID;
	}
	
	function updateShip($tracknum, $client, $carrier, $estdel, $status){
		
		//Establish variables and main query
		$count=0;
		$success = false;
		$update = "UPDATE alpineshipping.Shipment SET";
		
		//Determine which fields are filled and append the query
		if($client){
			$clientID=findClient($client);
			$update = $update. " ClientID='".$clientID."'";
			$count++;
		}
		if($carrier){
			$carrierID=findCarrier($carrier);
			if($count>0){
				$update = $update. ",";
			}
			$update=$update. " CarrierID='".$carrierID."'";
			$count++;
		}
		if($estdel){
			if($count>0){
				$update=$update. ",";
			}
			$update=$update. " EstDelivery='".$estdel."'";
			$count++;
		}
		if($status){
			if($count>0){
				$update=$update. ",";
			}
			$update=$update. " Status='".$status."'";
			$count++;
		}
		$update=$update. " WHERE TrackingNum='".$tracknum."';";
		
		//Connect to database and perform query
		$mysqli=conenctdb();
		if($result=$mysqli->query($update)){
			$success = true;
		}
		//Close the query and connection
		$mysqli->close();
		return $success;
	}
?>