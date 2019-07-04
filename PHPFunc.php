<?php
	session_start();  
	
	//Sanitize user input
	function check_input($data)
	{
	    $data = trim($data);
		$data = stripslashes($data);
	    $data = htmlspecialchars($data);
		//if($data ==""){
		//	$data = null;
		//}
	    return $data;
	}
	
	//Authenticate user attempting login
	function userAuth($uname,$pass){
	
		// Init count to 0
		$count=0;
		// Connect to the database
		$mysqli = connectdb();
		
		// Define the Query
		$Myquery = "SELECT * FROM alpineshipping.Users WHERE Username='".$uname."';"; 
		$result=$mysqli->query($Myquery);
		///Fetch the results of the query 	     
		while($row = $result->fetch_assoc() ){
			//verify password against stored password
			$passcheck = password_verify($pass, $row["Password"]);
			if($passcheck == true){
				$count=1;
			}
		}
		//close query and connection
		$result->close();	      
		$mysqli->close();
	    
		return $count;
	       
	}
	
	function verifyLevel($uname){
		$selectquery="SELECT * FROM alpineshipping.Users WHERE Username='".$uname."';";
		$mysqli=connectdb();
		$result=$mysqli->query($selectquery);
		while($row=$result->fetch_assoc()){
			$level=$row['AccessLevel'];
		}
		$result->close();
		$mysqli->close();
		return $level;
	}
	
	//Start Session
	function login($uname, $pass, $level){
		$_SESSION['uname'] = $uname;
		$_SESSION['pass'] = $pass;
		$_SESSION['level'] = $level;
		$time = $_SERVER['REQUEST_TIME'];
		$timeout_duration = 1800;  
		$_SESSION['LAST_ACTIVITY'] = $time;
	}
	//Logout user
	function logout($uname){
		unset($_SESSION['uname']);
		unset($_SESSION['pass']);
		echo"<h1>$uname, you have been successfully logged out.</h1>";
		session_destroy();
	}
	
	class ShipmentClass{
		// property declaration    
		private	$shipID = "";
		private $clientID = ""; 
		private $client = "";	 
		private $items = "";	 
		private $estdel = "";	 
		private $status = "";	 
		private $carrierID = "";	 
		private $carrier = "";	 
		private $tracknum = "";	 
		private $notes =  "";	 
		private $entered = "";
   
		// Constructor
		public function __construct($shipmentID, $clientID, $client, $items, $estdel, $status, $carrierID, $carrier, $tracknum, $notes, $entered){
			$this->shipmentID = $shipmentID;
			$this->clientID = $clientID;
			$this->client = $client;
			$this->items = $items;      
			$this->estdel = $estdel;
			$this->status = $status;
			$this->carrierID = $carrierID;
			$this->carrier = $carrier;
			$this->tracknum = $tracknum;
			$this->notes = $notes;
			$this->entered = $entered;
		}
		 // Get methods 
		public function getShipmentID(){
			return $this->shipmentID;
		} 
		public function getClientID(){
			return $this->clientID;
		} 
		public function getClient(){
			return $this->client;
		} 
		public function getItems(){
			return $this->items;
		} 
		public function getEstdel(){
			return $this->estdel;
		} 	 
		public function getStatus(){
			return $this->status;
		} 	 
		public function getCarrierID(){
			return $this->carrierID;
		} 	 
		public function getCarrier(){
			return $this->carrier;
		} 	 
		public function getTracknum(){
			return $this->tracknum;
		} 	 
		public function getNotes(){
			return $this->notes;
		} 	 
		public function getEntered(){
			return $this->entered;
		} 	   

		// Set methods 
		public function setShipmentID($value){
			$this->shipmentID = $value;    	
		}
		public function setClientID($value){
			$this->clientID = $value;    	
		}
		public function setClient($value){
			$this->client = $value;    	
		}
		public function setItems($value){
			$this->items = $value;    	
		}
		public function setEstdel($value){
			$this->estdel = $value;    	
		}  
		public function setStatus($value){
			$this->status = $value;    	
		}  
		public function setCarrierID($value){
			$this->carrierID = $value;    	
		}  
		public function setCarrier($value){
			$this->carrier = $value;    	
		}  
		public function setTracknum($value){
			$this->tracknum = $value;    	
		}  
		public function setNotes($value){
			$this->notes = $value;    	
		}  
		public function setEntered($value){
			$this->entered = $value;    	
		}  
	}
?>