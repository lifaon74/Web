<?php
require_once('aws/aws-autoloader.php');
//use Aws\Common\Aws;
//use Aws\Common\Credentials\Credentials;
//use Aws\DynamoDb\DynamoDbClient;

class DynamoDBItemAttribute {
	public $name;
	public $value;
	public $type;
	
	public function __construct($name, $value, $type) {
		$this->name		= $name;
		$this->value	= $value;
		$this->type		= $type;
	}
	
	public function toAWSFormat() {
		switch($this->type) {
			case 'boolean':
				return ['BOOL' => $this->value];
			break;
			case 'number':
				return ['N' => $this->value];
			break;
			case 'string':
				return ['S' => $this->value];
			break;
			case 'blob':
				return ['B' => $this->value . ""];
			break;
			default:
				trigger_error("\"" . $this->type . "\" is not a valid type");
				exit();
		}
	}
}

class DynamoDB {
	public $client;
	public $lastQueryConsumedCapacity;
	
	public function __construct($accessKeyId, $accessKey, $region) {	
		$config = [
			'key'		=> $accessKeyId,
			'secret'	=> $accessKey,
			'region'	=> $region
		];
	
		$aws = Aws\Common\Aws::factory($config);
		$this->client = $aws->get('dynamodb');
	}
	
	
	public function fromDynamoDBItemToAWSFormat($dynamoDBItem) {
		$AWSItem = [];
		
		foreach($dynamoDBItem as $dynamoDBItemAttribute) {
			$AWSItem[$dynamoDBItemAttribute->name] = $dynamoDBItemAttribute->toAWSFormat();
		}
		
		return $AWSItem;
	}
	
	public function fromAWSFormatToDynamoDBItem($AWSItem) {
		$dynamoDBItem = [];

		foreach($AWSItem as $AWSItemAttributeName => $AWSItemAttribute) {
			foreach($AWSItemAttribute as $type => $value) {
				switch($type) {
					case 'BOOL':
						$dynamoDBItem[$AWSItemAttributeName] = new DynamoDBItemAttribute($AWSItemAttributeName, $value, 'boolean');
					break;
					case 'N':
						$dynamoDBItem[$AWSItemAttributeName] = new DynamoDBItemAttribute($AWSItemAttributeName, $value, 'number');
					break;
					case 'S':
						$dynamoDBItem[$AWSItemAttributeName] = new DynamoDBItemAttribute($AWSItemAttributeName, $value, 'string');
					break;
					case 'B':
						$dynamoDBItem[$AWSItemAttributeName] = new DynamoDBItemAttribute($AWSItemAttributeName, base64_decode($value), 'blob');
					break;
					default:
						trigger_error("\"" . $type . "\" is not a valid type");
						exit();
				}
			}
		}
		
		return $dynamoDBItem;
	}
		
	
	public function describeTable($tableName) {
		$command = [
			'TableName' => $tableName
		];
		
		$result = $this->client->describeTable($command);
		
		$table = [
			'name'		=> $tableName,
			'hashKey'	=> $result['Table']['KeySchema'][0]['AttributeName'],
			'status'	=> $result['Table']['TableStatus']
		];
		
		return $table;
	}
	
	public function clearTable($tableName) {
		$tableDescription = $this->describeTable($tableName);
		$hashKeyName = $tableDescription['hashKey'];
		
		$items = $this->listAllItemKey($tableName, $hashKeyName);
		
		foreach($items as $item) {
			$this->delete($tableName, $item[$hashKeyName]);
		}
	}
	
	public function listAllItemKey($tableName, $hashKeyName = NULL) {
		if($hashKeyName === NULL) {
			$tableDescription = $this->describeTable($tableName);
			$hashKeyName = $tableDescription['hashKey'];
		}
		
		$command = [
			'TableName' => $tableName,
			'AttributesToGet' => [$hashKeyName],
			'ReturnConsumedCapacity' => 'TOTAL'
		];
		
		$result = $this->client->scan($command);
		
		$this->lastQueryConsumedCapacity = $result['ConsumedCapacity']['CapacityUnits'];
		
		$dynamoDBItems = [];
		foreach($result['Items'] as $AWSItem) {
			$dynamoDBItems[] = $this->fromAWSFormatToDynamoDBItem($AWSItem);
		}
		return $dynamoDBItems;
	}
	
	public function getLastQueryConsumedCapacity() {
		return $this->lastQueryConsumedCapacity;
	}
	
	
	public function insert($tableName, $dynamoDBItems) {
		$command = [
			'TableName' => $tableName,
			'Item' => $this->fromDynamoDBItemToAWSFormat($dynamoDBItems),
			'ReturnConsumedCapacity' => 'TOTAL'
		];
		
		$result = $this->client->putItem($command);
		//print_r($result);
		
		$this->lastQueryConsumedCapacity = $result['ConsumedCapacity']['CapacityUnits'];
	}
	
	public function delete($tableName, $dynamoDBKeyItem) {
		$command = [
			'TableName' => $tableName,
			'Key' => $this->fromDynamoDBItemToAWSFormat([$dynamoDBKeyItem]),
			'ReturnConsumedCapacity' => 'TOTAL'
		];
		
		$result = $this->client->deleteItem($command);
		
		$this->lastQueryConsumedCapacity = $result['ConsumedCapacity']['CapacityUnits'];
	}
	
	public function get($tableName, $dynamoDBKeyItem) {
		$command = [
			'TableName' => $tableName,
			'Key' => $this->fromDynamoDBItemToAWSFormat([$dynamoDBKeyItem]),
			'ReturnConsumedCapacity' => 'TOTAL'
		];

		$result = $this->client->getItem($command);
		//print_r($result);
		
		$this->lastQueryConsumedCapacity = $result['ConsumedCapacity']['CapacityUnits'];
		
		if(isset($result['Item'])) {
			return $this->fromAWSFormatToDynamoDBItem($result['Item']);
		} else {
			return null;
		}
	}

}


$dynamoDB = new DynamoDB($_CONST_DYNAMODB_ACCESS_ID, $_CONST_DYNAMODB_ACCESS_KEY, $_CONST_DYNAMODB_REGION);
?>