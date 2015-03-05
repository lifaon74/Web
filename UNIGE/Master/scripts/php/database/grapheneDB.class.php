<?php

if(!isset($fnc)) { require_once('../../../scripts/php/fnc.php'); }
require_once($fnc->absolutePath('scripts/php/database/graphenedb/vendor/autoload.php'));

//use Everyman\Neo4j\Cypher\Query;
	
class GrapheneDB {
	public $client;
	
	public function __construct($host, $port, $database, $key) {
		$this->client = new Everyman\Neo4j\Client($host, $port);
		$this->client->getTransport()->setAuth($database, $key);
	}
	
	public function clear() {
		/*$queryString = "
			START n = node(*)
			OPTIONAL MATCH n-[r]-() 
			WHERE (ID(n)>0 AND ID(n)<10000) 
			DELETE n, r
		";*/
		
		$queryString = "
			MATCH (n)-[r]-()
			DELETE n, r
		";
		
		$result = $this->query($queryString);
		
		$queryString = "
			MATCH (n)
			DELETE n
		";
		
		$result = $this->query($queryString);
	}
	
	
	public function query($query, $arguments = []) {
		$query = new Everyman\Neo4j\Cypher\Query($this->client, $query, $arguments);
		return $query->getResultSet();
	}	
}

class ObjectQueries extends GrapheneDB {

	/*print_r($result[0]['r']->getType());
	print_r($result[0]['n2']->getProperty('objectID'));*/
		
	public function addObject($objectID) {
		$queryString = "
			CREATE (n1:Object {objectID:{objectID}})
			RETURN n1
		";
		
		$result = $this->query($queryString, ['objectID' => $objectID]);
		return $result[0]['n1'];
	}
	
	public function getObject($objectID) {
		$queryString = "
			MATCH (n1)
			WHERE n1.objectID = {objectID}
			RETURN n1
		";
		
		$result = $this->query($queryString, ['objectID' => $objectID]);
		
		return $result[0]['n1'];
	}
	
	
	public function createRelationship($objectID_1, $relationship, $objectID_2) {
		$queryString = "
			MATCH (n1), (n2)
			WHERE n1.objectID = {objectID_1} AND n2.objectID = {objectID_2} AND NOT((n1)-[:" . $relationship . "]-(n2))
			CREATE(n1)-[:" . $relationship . " {activated:0}]->(n2)
			RETURN n1, n2
		";
		
		$result = $this->query($queryString, ['objectID_1' => $objectID_1, 'objectID_2' => $objectID_2]);
	}
	
	public function removeRelationship($objectID_1, $relationship, $objectID_2) {
		$queryString = "
			MATCH (n1)-[r:" . $relationship . "]-(n2)
			WHERE n1.objectID = {objectID_1} AND n2.objectID = {objectID_2}
			DELETE r
		";
		
		$result = $this->query($queryString, ['objectID_1' => $objectID_1, 'objectID_2' => $objectID_2]);
	}
	
	public function relationshipExists($objectID_1, $relationship, $objectID_2) {
		$queryString = "
			MATCH (n1)-[r]-(n2)
			WHERE n1.objectID = {objectID_1} AND n2.objectID = {objectID_2}
			RETURN n1, r, n2
		";
		
		$result = $this->query($queryString, ['objectID_1' => $objectID_1, 'objectID_2' => $objectID_2]);
		if(count($result) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getRelationships($objectID) {
		$queryString = "
			MATCH (n1)-[r]-(n2)
			WHERE n1.objectID = {objectID}
			RETURN n1, r, n2
		";
		
		$result = $this->query($queryString, ['objectID' => $objectID]);
		
		$return = [];
		foreach($result as $row) {
			$relationName = $row['r']->getType();
			$objectID_2 = $row['n2']->getProperty('objectID');
			
			if(!isset($return[$objectID_2])) { $return[$objectID_2] = []; } 
			
			$return[$objectID_2][] = $relationName;
		}
		
		return $return;
	}
	
	
}

$grapheneDB = new GrapheneDB('thingbook.sb02.stations.graphenedb.com', 24789, 'thingbook', 'ff0lmru9Inh57K0CIQw4');

/*$ObjectQueries = new ObjectQueries();

echo "result : \n";

$ObjectQueries->clear();

for($i = 0; $i < 5; $i++) {
	$ObjectQueries->addObject($i);
}

//$ObjectQueries->getObject(0);
$ObjectQueries->createRelationship(0, 'amis', 1);
$ObjectQueries->createRelationship(0, 'amis', 1);
$ObjectQueries->createRelationship(0, 'friend', 1);
$ObjectQueries->createRelationship(0, 'amis', 2);
print_r($ObjectQueries->getRelationships(0));

$ObjectQueries->removeRelationship(0, 'friend', 1);
print_r($ObjectQueries->getRelationships(0));*/

	
?>