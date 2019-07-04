<?php
	//include file with general php functions
	require_once('PHPFunc.php');
	
	//Establish global variables
	static $host = 'localhost';
	static $db = 'alpineshipping';
	
	
	//General login
	function connectdb(){      		
		// Get the DBParams
		$mydbparms = getDbparms();
		$success = false;
		// Try to connect
		$mysqli = new mysqli($mydbparms->getHost(), $mydbparms->getUsername(), 
	                        $mydbparms->getPassword(),$mydbparms->getDb());
	
		if ($mysqli->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') '
	            . $mysqli->connect_error);      
		}else{
		}
		return $mysqli;
	}
	
	//function retrive preset db login info
	function getDbparms(){
	 	$trimmed = file('dbparms.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$key = array();
		$vals = array();
		foreach($trimmed as $line){
			$pairs = explode("=",$line);    
			$key[] = $pairs[0];
			$vals[] = $pairs[1]; 
		}
		// Combine Key and values into an array
		$mypairs = array_combine($key,$vals);
	
		// Assign values to ParametersClass
		$myDbparms = new DbparmsClass($mypairs['username'],$mypairs['password'],
	                $mypairs['host'],$mypairs['db']);
	
		// Display the Paramters values
		return $myDbparms;
	}

	// Class to construct Database parameters with getters/setter
	class DBparmsClass{
		
	    // property declaration  
	    private $username="";
	    private $password="";
	    private $host="";
	    private $db="";
	   
	    // Constructor
	    public function __construct($myusername,$mypassword,$myhost,$mydb){
			$this->username = $myusername;
			$this->password = $mypassword;
			$this->host = $myhost;
			$this->db = $mydb;
	    }
	    
	    // Get methods 
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
	
	    // Set methods 
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
	
	//Connect individual users
	function indconnectdb($uname, $pass){
		$mysqli= new mysqli($host, $uname, $pass, $db);
		
		if ($mysqli->connect_error){
			die(Header('Location: FailedLogin.php'));
		}
		return $mysqli;
	}
	
	//function to enter a new shipment 
	function insertShipment($client, $carrier, $items, $shipdate, $deliverydate, $tracknum, $status){
		
		//establish variable to measure success
		$success=false;
		$date = date_create();
		$dateNow = date_format($date,"Y-m-d"); 		
		// Connect to the database
		$mysqli = connectdb();
		
	    //find clientID based on BusinessName submitted	   
		$clientID = findClient($client);
			
		//find carrierID based on Carrier name submitted
		$carrierID = findCarrier($carrier);
		
		//create and prepare query
		$insertQuery = "INSERT INTO alpineShipping.Shipment (ClientID, CarrierID, ItemsShipping, EstShipDate, EstDelivery, TrackingNum, Status, DateEntered) 
				VALUES ('".$clientID."', '".$carrierID."', '".$items."', '".$shipdate."', '".$deliverydate."', '".$tracknum."', '".$status."', '".$dateNow."');";
		
		//fetch the results of third query and determine successful execution
		$mysqli->query($insertQuery);
		$thirdresult=$mysqli->affected_rows;
		if($thirdresult>0){
			$success=true;
		}
		//close query and connection
		$thirdresult->close();
		$mysqli->close();
		return $success;
	}

	//function to enter new client
	function addClient($business){
		//Establish success variable
		$success=false;
		//prepare statements
		$select="SELECT * FROM alpineshipping.Client WHERE BusinessName='".$business."';";
		$query="INSERT INTO alpineshipping.Client (BusinessName) VALUES ('".$business."');";
		
		//Establish connection
		$mysqli=connectdb();
		
		//Check for duplicate entries
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
		//return result
		return $success;
	}
	
	//function to return a list of shipments
	function searchShipments($limit, $order, $client, $carrier, $startdate, $enddate, $status, $tracknum){
	
		//prepare main select statement
		$select = "SELECT Shipment.ShipmentID, Client.ClientID, Client.BusinessName, Shipment.ItemsShipping, Shipment.EstDelivery, Shipment.Status, Carrier.CarrierID,
			Carrier.CarrierName, Shipment.TrackingNum, Shipment.Notes, Shipment.DateEntered FROM ((Shipment INNER JOIN Client ON Client.ClientID=Shipment.ClientID)
			JOIN Carrier ON Carrier.CarrierID=Shipment.CarrierID) ";
		//variable to track filled fields
		$count=0;

		//determine which fields have been filled
		if($client){
			//detemine clientID
			$clientID = findClient($client);
			//append $select
			$select= $select. "WHERE Shipment.ClientID='".$clientID."' ";
			$count++;
		}
		if($carrier){
			//determine carrier ID
			$carrierID = findCarrier($carrier);
			//determine if previous additions were made to the query
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
		//assign limit
		$select = $select. "ORDER BY DateEntered ".$order." LIMIT ".$limit.";";
		
		//establish connection
		$mysqli=connectdb();
		//retrieve search results
		if($result=$mysqli->query($select)){
			while($row=$result->fetch_assoc()){
				$listShipments[]=$row['ShipmentID'];
			}
		}
		
		//Close query and connection
		$mysqli->close();
		
		return $listShipments;
	}
	
	//function to display search results
	function viewShipments($shipID){
		
		//ensure $allShipments is empty
		$allShipments="";
		//establish selectg query
		$select = "SELECT Shipment.ShipmentID, Client.ClientID, Client.BusinessName, Shipment.ItemsShipping, Shipment.EstDelivery, Shipment.Status, Carrier.CarrierID,
				Carrier.CarrierName, Shipment.TrackingNum, Shipment.Notes, Shipment.DateEntered FROM ((Shipment INNER JOIN Client ON Client.ClientID=Shipment.ClientID)
				JOIN Carrier ON Carrier.CarrierID=Shipment.CarrierID) WHERE ShipmentID = '".$shipID."';";
				
		//establish connection		
		$mysqli=connectdb();
		
		//store results in an array
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
		//close all queries and connections
		$mysqli->close();
		
		//return the object containing the shipment
		return $allShipments;
	}
	
	//function to populate client dropdown
	function clientDropdown(){
		//Populate Query
		$clientQuery= "SELECT BusinessName FROM Client;";
		
		//Establish connection and run query
		$mysqli=connectdb();
		$results=$mysqli->query($clientQuery);
		
		//Populate options to drop down
		while($row=$results->fetch_assoc()){
			echo "<option value='" . $row['BusinessName'] . "'>" . $row['BusinessName'] . "</option>";
		}
		//close query and connection
		$results->close();
		$mysqli->close();
	}
	
	//function to populate carrier drop down
	function carrierDropdown(){
		//Populate query
		$carrierQuery= "SELECT CarrierName FROM Carrier;";
		//Establish connectionand run query
		$mysqli=connectdb();
		$results=$mysqli->query($carrierQuery);
		
		//Populate options to drop down
		while($row=$results->fetch_assoc()){
			echo "<option value='" . $row['CarrierName'] . "'>" .$row['CarrierName'] ."</option>";
		}
		//close query and connection
		$results->close();
		$mysqli->close();
	}
	
	//function to determine clientID
	function findClient($business){
		
		//prepare initial query to determine client ID
		$selectQuery = "SELECT ClientID FROM Client WHERE BusinessName='".$business."';";
		
		//establish connection
		$mysqli=connectdb();
		//Run query
		$results=$mysqli->query($selectQuery);
		while($row = $results->fetch_assoc()){
			$clientID=$row['ClientID'];
		}
		//Close query
		$mysqli->close();
		
		//return result
		return $clientID;
	}
	
	//function to determine carrierID
	function findCarrier($carrier){
		
		//prepare query to determine carrierID
		$selectQuery = "SELECT CarrierID FROM Carrier WHERE CarrierName='".$carrier."';";
		
		//Establish connection
		$mysqli=connectdb();
		//Run query
		$results=$mysqli->query($selectQuery);
		while($row=$results->fetch_assoc()){
			$carrierID=$row['CarrierID'];
		}
		//close query
		$results->close();
		
		//return result
		return $carrierID;
	}
	
	function updateShip($tracknum, $client, $carrier, $estdel, $status){
		
		//set variables and main query
		$count=0;
		$success = false;
		$update = "UPDATE alpineshipping.Shipment SET";
		
		//determine which fields are filled and append the main query
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
		
		//conect to database and run query
		$mysqli=conenctdb();
		if($result=$mysqli->query($update)){
			$success = true;
		}
		//close the connection
		$mysqli->close();
		
		//return the result
		return $success;
	}
?>