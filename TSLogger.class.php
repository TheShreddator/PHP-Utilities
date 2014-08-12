<?php
////////////////////////////////////////////////////////////////////////////////////////
//  Class Name:		TSLogger
//	Last Update:	08/12/2014 @ 5:30 PM
//  Description:	Tracks users by recording info stored in the $_SERVER array
//					Also stores user location data using the IPInfoDB.com API
//	Example:		require_once('TSLogger.class.php');
//					$TSLogger = new TSLogger(); 
/////////////////////////////////////////////////////////////////////////////////////////
require_once("TSDatabase.class.php");

class TSLogger {
		
	protected $DB;  // Database Object
	private $api_key = "xxxx"; // API Key for IPInfoDB.com
	
	/////////////////////////////////////////////////////////////////////////////
	//  __construct(string $host, string $user, string $pass, string $db)
	//  Description:  Connect to DB using default values if necessary
	/////////////////////////////////////////////////////////////////////////////
	public function __construct($host = "localhost", $user = "root", $pass = "password", $db = "database"){
		// echo "TSLogger created! <br />";
		$this -> connect($host, $user, $pass, $db);
		$this -> insertLog();
	} 
	
	
	
	///////////////////////////////////////////////////////////////////////
	//	Connect(string $host, string $user, string $pass, string $db)
	//	Description:  Establish database connection
	//				  Create user row
	///////////////////////////////////////////////////////////////////////	
	private function connect($host, $user, $pass, $db){
		if($this -> DB = new TSDatabase("{$host}", "{$user}", "{$pass}", "{$db}")){
			// echo "Connection ... Successful <br />";
		}
	}
	
	///////////////////////////////////////////////////////////////////////
	//	insertLog()
	//	Description:  Create blank user row
	///////////////////////////////////////////////////////////////////////
	private function insertLog(){
		if($this -> DB -> query("INSERT INTO `web` (`id`) VALUES (NULL);")){
			// echo "User Created ... Successful <br />";
			$this -> updateLog();
		} else {
			// echo "User NOT Created ... Failure! <br />";
		}
		
	}
	
	///////////////////////////////////////////////////////////////////////
	//	updateLog()
	//	Description:  Update using $_SERVER values
	///////////////////////////////////////////////////////////////////////
	private function updateLog(){
		$results = $this -> DB -> query("SELECT MAX(id) FROM web");
		$row = $this -> DB -> fetch_array($results);
		$web_id = $row[0];
		foreach($_SERVER as $key => $val){
			$this -> DB -> query("UPDATE web SET {$key} = '{$val}' WHERE id = '{$web_id}'");
		}
		// echo "User Updated ... Successful <Br />";
		$this -> updateUserInfo($web_id);	
	}
	
	///////////////////////////////////////////////////////////////////////
	//	updateUserInfo($web_id)
	//	Description:	update user row using IP and Geolocation	
	///////////////////////////////////////////////////////////////////////
	private function updateUserInfo($web_id){
		$user_ip = $_SERVER['REMOTE_ADDR'];
		$tmp = fopen('http://api.ipinfodb.com/v3/ip-city/?key='. $this -> api_key .'&ip=' . $user_ip, "rb");  
		$tmp= stream_get_contents($tmp);
		$location = explode(";", $tmp);
		$this -> DB -> query("UPDATE web SET 
			USER_LOCATION = '{$tmp}',
			USER_COUNTRY = '{$location[4]}', 
			USER_STATE = '{$location[5]}', 
			USER_CITY = '{$location[6]}', 
			USER_ZIPCODE = '{$location[7]}' 
			WHERE id = '{$web_id}'");
		// echo "User Location Updated ... Successful <br />";
	}
}
