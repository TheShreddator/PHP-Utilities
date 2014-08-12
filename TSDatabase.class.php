<?php
///////////////////////////////////////////////////////////////////////////////////////////////////
//  Class Name:		TSDatabase
//	Last Update:	08/12/2014 @ 5:40 PM
//  Description:	Creates a Database object for use by other classes or processes
//	Example:		require_once('TSDatabase.class.php');
//					$TSDatabase = new TSDatabase('host_ip', 'user_name', 'password', 'database');
//					$results = $TSDatabase -> query("SELECT * FROM table");
//					while($row = $TSDatabase -> fetch_array($results)){
//						echo $row[0];
//					} 
///////////////////////////////////////////////////////////////////////////////////////////////////
class TSDatabase {
	
	//Instance Variables
	private $db;
	
	//Create Database object
	public function __construct($host, $user, $pass, $db_name){
		$this->db = mysql_connect($host, $user, $pass);
		if (!$this->db) {
			die("Database connection failed: " . mysql_error());
		} else {
			$query = "CREATE DATABASE IF NOT EXISTS ". $db_name ."";
			mysql_query($query);
			$db_select = mysql_select_db($db_name, $this -> db);
			if (!$db_select) {
				die("Database selection failed: " . mysql_error());
			}
		}
	}
	
	//Process query so it is acceptable for input into SQL
	public function mysql_prep( $value ) {
		$magic_quotes_active = get_magic_quotes_gpc();
		$new_enough_php = function_exists( "mysql_real_escape_string" ); // i.e. PHP >= v4.3.0
		if( $new_enough_php ) { // PHP v4.3.0 or higher
			// undo any magic quote effects so mysql_real_escape_string can do the work
			if( $magic_quotes_active ) { $value = stripslashes( $value ); }
			$value = mysql_real_escape_string( $value );
		} else { // before PHP v4.3.0
			// if magic quotes aren't already on then add slashes manually
			if( !$magic_quotes_active ) { $value = addslashes( $value ); }
			// if magic quotes are active, then the slashes already exist
		}
		return $value;
	}
	
	//Perform query
	public function query($sql) {
		$result = mysql_query($sql, $this->db);
		$this->confirm_query($result);
		return $result;
	}
	
	//Confirm query
	private function confirm_query($result) {
		if (!$result) {
			die("Database query failed: " . mysql_error());
		}
	}
	
	//Fetch array using result
  	public function fetch_array($result_set) {
		return mysql_fetch_array($result_set);
  	}
  	
  	//Fetch associative array using result
  	public function fetch_assoc($result_set) {
		return mysql_fetch_assoc($result_set);
  	}
  	
  	//Get the number of rows in the result
  	public function num_rows($result_set) {
   		return mysql_num_rows($result_set);
  	}
  	
	
	//Close the connection
	public function close_connection() {
		if(isset($this->db)) {
			mysql_close($this->db);
			unset($this->db);
			//echo "<br /> DECON";
		}
	}
}