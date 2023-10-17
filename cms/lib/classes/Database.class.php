<?php
$dbLink=null;

class Database{
	public function connect(){
		global $dbLink;
		$dbLink = mysqli_connect("localhost", "coreycompressor", "DellPEt320", "coreycompressor");
		if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
		if (!mysqli_select_db($dbLink, "coreycompressor")){
		
			exit('Fatal Error: could not select the database');
		}
	}
	
	private function disconnect(){
		global $dbLink;
		if (!mysqli_close($dbLink)){
			exit('Fatal Error: failed to disconnect from database');
		}
	}
	
	public function query($sql){
		global $dbLink;
		if($dbLink==null) $this->connect();
		$result = mysqli_query($dbLink, $sql);	
		if (!$result){
			return false;
		}
		
		return $result;
	}
	public function lastID(){
		global $dbLink;
		if($dbLink==null) $this->connect();
		return  mysqli_insert_id();
	}
	public function getRows($sql){
		global $dbLink;
		if($dbLink==null) $this->connect();
		$result = $this->query($sql);
		if ($result == false){
			//Query failed
			return false;
		}
		
		$rows = Array();
		while ($row = mysqli_fetch_assoc($result)){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	public function getNumRows($sql){
		global $dbLink;
		if($dbLink==null) $this->connect();
		$result = $this->query($sql);
		if ($result == false){
			//Query failed
			return false;
		}
		
		return mysqli_num_rows($result);
	}
	
	public function transQuery($sql){
		global $dbLink;
		if($dbLink==null) $this->connect();
		$this->query("BEGIN WORK;");
		
		$result = $this->query($sql);
		if ($result == false){
			//Query failed
			$this->query("ROLLBACK;");
			return false;
		}
		
		$this->query("COMMIT;");
		return true;
	}
	
	static public function dbEscape($data){
		global $dbLink;
		if($dbLink==null) $this->connect();
		$data = strip_tags($data);
		$data = html_entity_decode($data, ENT_QUOTES);
		$data = stripslashes($data);
		$data = mysqli_real_escape_string($data);
		return $data;
	}
	
	static public function pageEscape($data, $toHTML){
		global $dbLink;
		if($dbLink==null) $this->connect();
		$data = stripslashes($data);
		if ($toHTML === true){
			$data = htmlentities($data, ENT_QUOTES);
			$data = nl2br($data);
		}
		$data = addslashes($data);
	}
}

?>