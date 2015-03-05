<?php

class mySQL {
	public $databaseName;
	public $client;
	public $transactionLevel;
	
	public function __construct($host, $user, $password, $databaseName) {
		$this->databaseName		= $databaseName;
		$this->client			= new PDO("mysql:host=" . $host . ";dbname=" . $this->databaseName, $user, $password);
		$this->transactionLevel	= 0;
		
		$this->client->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$query = $this->client->prepare("SET NAMES UTF8;");
		$query->execute();
	}
	
	public function replaceVariablesInQuery($queryString) {
		return preg_replace("#\{(\w+)\}#U", ":$1", $queryString);
	}
	
	public function quote($string) {
		return $this->client->quote($string);
	}
	
	
	public function listTables() {
		$returnedTables = [];
		$tables = $this->query("SHOW TABLES FROM " . $this->databaseName);
		foreach($tables as $table) {
			$returnedTables[] = $table[0];
		}
		return $returnedTables;
	}
	
	public function listAttributes($tableName) {
		$attributes = $this->query("SHOW COLUMNS FROM " . $tableName);
		return $attributes;
	}
	
	public function clear() {
		$tables = $this->listTables();
		foreach($tables as $table) {
			$this->clearTable($table);
		}
	}
	
	public function clearTable($tableName) {
		$this->query("TRUNCATE " . $tableName);
	}
	
		// transactions
	public function beginTransaction() {
		if($this->transactionLevel == 0) {
			$this->query("START TRANSACTION");
			$this->transactionLevel++;
			return true;
		} else if($this->transactionLevel > 0) {
			$this->query("SAVEPOINT LEVEL" . $this->transactionLevel);
			$this->transactionLevel++;
			return true;
		} else {
			$this->transactionLevel = 0;
			return false;
		}
	}
	
	public function commit() {
		$this->transactionLevel--;
		
		if($this->transactionLevel == 0) {
			$this->query("COMMIT");
			return true;
		} else if($this->transactionLevel > 0) {
			$this->query("RELEASE LEVEL" . $this->transactionLevel);
			return true;
		} else {
			$this->transactionLevel = 0;
			return false;
		}
	}
	
	public function rollBack() {
		$this->transactionLevel--;
		
		if($this->transactionLevel == 0) {
			$this->query("ROLLBACK");
			return true;
		} else if($this->transactionLevel > 0) {
			$this->query("ROLLBACK TO SAVEPOINT LEVEL" . $this->transactionLevel);
			return true;
		} else {
			$this->transactionLevel = 0;
			return false;
		}
	}
	
	
	public function transaction($callback) {
		$this->beginTransaction();
		
		$result = $callback($this);
		
		if($result === null || $result === true) {
			$this->commit();
		} else {
			$this->rollBack();
		}
	}
	
	public function query($queryString, $arguments = [], $waitingForAResponse = false) {
		$queryString = $this->replaceVariablesInQuery($queryString);
		$query = $this->client->prepare($queryString);
		
		if(count($arguments) > 0) {
			$query->execute($arguments);
		} else {
			$query->execute();
		}
		
		if($waitingForAResponse) {
			return $query->fetchAll();
		} else {
			return null;
		}
	}
	
	public function select($queryString, $arguments = []) {
		return $this->query($queryString, $arguments, true);
	}
	
	public function insert($tableName, $values) {
		$attributes = "";
		
		for($i = 0; $i < count($values); $i++) {
			if($i > 0) { $attributes .= ", "; }
			$attributes .= "?";
		}
		
		$queryString = "INSERT INTO " . $tableName . " VALUES (" . $attributes . ")";
		$this->query($queryString, $values, false);
	}
	
}

$mySQL = new mySQL('127.0.0.1', 'Administrateur', 'Pa$$W0rd', 'thingbook');

//$mySQL->insert('object', ['15', '0', 'd', 'lumiere']);
/*$result = $mySQL->query("SELECT * FROM object WHERE obj_name = {name}", ['name' => 'd']);
print_r($result);*/
?>