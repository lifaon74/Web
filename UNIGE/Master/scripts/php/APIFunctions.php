<?php

if(!isset($fnc)) { require_once('../../scripts/php/fnc.php'); }

require_once($fnc->absolutePath('scripts/php/database/mySQL.class.php'));
require_once($fnc->absolutePath('scripts/php/database/grapheneDB.class.php'));
require_once($fnc->absolutePath('scripts/php/database/dynamoDB.class.php'));
require_once($fnc->absolutePath('scripts/php/bitString.class.php'));


class APIFunctions {
	public $mySQL, $grapheneDB, $dynamoDB;
	public $commands;
	public $errorAppend;
	
	public function __construct() {
		global $mySQL, $grapheneDB, $dynamoDB;
		$this->mySQL = $mySQL;
		$this->grapheneDB = $grapheneDB;
		$this->dynamoDB = $dynamoDB; 
		
		$this->errorAppend = false;
		
		$this->initCommands();
		
		//echo "[\"" . $objectAPIFunctions->generateRandomKey()->base64Encode() . "\", \"" . $objectAPIFunctions->generateRandomKey()->base64Encode() . "\"]";
		
		$this->objectCredentials = [
			["zjFMRIZw3hecoss7EDkbKa0bO8w3xyAK16SeWkS8EDc=", "cgsqvSsKKvS21K+y60faqDIt312P0aJraPrZk1+adn4="],
			["bL7FbHPFU6T2GvVibTnespXKHTg1Sea5P2m5bEu1Lts=", "UEFWnjv31PPMaiCNUbkBhaky2j4TsjMsXZFqK3UN99Q="],
			["Wb+rfDY8aX0r+uxzR3GsYY7TjBQg5eOiwMyJEuOkJu4=", "xfH59IwSDSyWzH46Xm+vsCfl6NX4m4Uz3/Jvy27SZSc="]
		];
		
		$this->userCredentials = [
			["Pur93ZYxDZHwMhLVm5HBTteinJuK+uB2KF69+rAHmiw=", "lZsH+cvqH2yjhe5yON1wEsTFUZvHFFIK5U3ur3wpBLg=", "valentinrich@gmail.com", "abc"],
			["v7nENLOW2Gov4RYnuzssqMVfPeOVb+ZemLv4RogKCgE=", "ArzDiIWxsIUm9BkGeJbljGss8z9quzUZESvUv1slMbw=", "a@b.c", "def"],
			["e0hqhHnxFDniKfOA/OlKweZVkW/ALhhhHQQVJCJa+wc=", "C6MPyJzdfg78zFamuF5EdtTjzvlyKkY9dAuKdX/zgm4=", "test", "test"]
		];
	}

	
/**
	INIT
**/

	public function initCommands() {
		$this->commands = [
				// registration
			"register_user" => [
				"callback"		=> function($APIFunctions, $query) {
					$user = $APIFunctions->registerUser($query->parameters->email, $query->parameters->password);
					if($user === null) {
						return $this->generateError(130, "user registration failed");
					} else {
						return $user;
					}
				}
			],
			
			"register_object" => [
				"callback"		=> function($APIFunctions, $query) {
					$object = $APIFunctions->registerObject($query->parameters->id, $query->parameters->key, $query->parameters->name , $query->parameters->type);
					if($object === null) {
						return $this->generateError(131, "object registration failed");
					} else {
						return $object;
					}
				}
			],
			
				// notifications
			"get_notifications" => [
				"authenticate"	=> ["object", "user"],
				"callback"		=> function($APIFunctions, $query) {
					return [
						"notifications" => $APIFunctions->getNotifications($query->id)
					];
				}
			],
			
			"answer_notification" => [
				"authenticate"	=> ["object", "user"],
				"callback"		=> function($APIFunctions, $query) {
					$answered = $APIFunctions->answerNotification($query->id, $query->parameters);
					if(!$answered) {
						$this->_generateError(110);
					} else {
						return [];
					}
				}
			],
			
			
				// publications
			"post_publication" => [
				"authenticate"	=> ["object"],
				"callback"		=> function($APIFunctions, $query) {
					return [
						"publication_id" => $APIFunctions->postPublication($query->id, $query->parameters->publication, $query->parameters->to_object)
					];
				}
			],
			
			"get_publication" => [
				"authenticate"	=> ["object"],
				"callback"		=> function($APIFunctions, $query) {
					return [
						"publication" => $APIFunctions->getPublication($query->id, $query->parameters->publication_id)
					];
				}
			],
			
			"get_publications" => [
				"authenticate"	=> ["object"],
				"callback"		=> function($APIFunctions, $query) {
					return [
						"publications" => $APIFunctions->getPublications($query->id, $query->parameters)
					];
				}
			],
			
			"remove_publication" => [
				"authenticate"	=> ["object"],
				"callback"		=> function($APIFunctions, $query) {
					$removed = $APIFunctions->removePublication($query->id, $query->parameters->publication_id);
					if(!$removed) {
						$this->_generateError(120);
					} else {
						return [];
					}
				}
			],
			
				// user
			"authenticate" => [
				"callback"		=> function($APIFunctions, $query) {
					$user = $APIFunctions->authenticateUser($query->parameters->email, $query->parameters->password);
					if($user === null) {
						return $this->generateError(140, "user authentication failed");
					} else {
						return $user;
					}
				}
			],
			
			"list_owned_objects" => [
				"authenticate"	=> ["user"],
				"callback"		=> function($APIFunctions, $query) {
					return [
						"owned_objects" => $APIFunctions->listOwnedObjects($query->id)
					];
				}
			],
			
				// owner
			"request_for_a_new_owner" => [
				"authenticate"	=> ["object"],
				"callback"		=> function($APIFunctions, $query) {
					$notificationID = $APIFunctions->requestForANewOwner($query->id, $query->parameters->user_id);
					if($notificationID) {
						return [
							"notification_id" => $notificationID
						];
					} else {
						$this->_generateError(130);
					}
				}
			],
			
				// relationship
			"request_for_a_new_relationship" => [
				"authenticate"	=> ["object"],
				"callback"		=> function($APIFunctions, $query) {
					$APIFunctions->requestForANewRelationship($query->id, $query->parameters->relationship, $query->parameters->with_object);
					return [];
				}
			],
			
			"get_relationships" => [
				"authenticate"	=> ["object"],
				"callback"		=> function($APIFunctions, $query) {
					return [
						"relationships" => $APIFunctions->getRelationships($query->id, $query->parameters->of_object)
					];
				}
			],
			
			"remove_relationship" => [
				"authenticate"	=> ["object"],
				"callback"		=> function($APIFunctions, $query) {
					$APIFunctions->removeRelationshipBetweenObjects($query->id, $query->parameters->relationship, $query->parameters->with_object);
					return [];
				}
			]
		];
	}

/**
	Reply to query
**/
	public function executeQueryFromREQUEST() {
		if(isset($_REQUEST['query'])) {
			$query = json_decode($_REQUEST['query']);
			$error = getJSONLastError();
			
			if($error === null) {
				return $this->executeQuery($query);
			} else {
				return $this->generateError(5, "error while parsing JSON : " . $error);
			}
		} else {
			return $this->generateError(10, "missing \"query\" attribute");
		}
	}
	
	public function executeQuery($query) {
		$this->errorAppend = false;
		
		if(isset($this->commands[$query->action])) {
			$command		= $this->commands[$query->action];
			
			$authenticate	= false;
			$entity			= null;
			
			if(isset($command['authenticate'])) {
				$entity = $this->getEntity($query->id);
				if($entity === null) {
					$authenticate = false;
				} else {
					if($query->key == $entity->key) {
						if(in_array($entity->type, $command['authenticate'])) {
							$authenticate = true;
						} else {
							return $this->generateError(101, "your account type isn't valid for this action");
						}
					} else {
						$authenticate = false;
					}
				}
			} else {
				$authenticate = true;
			}
			
			if($authenticate) {
				$reponse = (object) $command['callback']($this, $query, $entity);
				
				if($this->errorAppend) {
					$reply		= $reponse;
				} else {
					$reply		= (object) [
						'code'		=> 0,
						'response'	=> $reponse
					];
				}
				
				return $reply;
			} else {
				return $this->generateError(100, "authentication failed");
			}
		} else {
			return $this->generateError(10, "unknown value for \"action\" attribute");
		}
	}
	
	private function generateError($code, $message) {
		/*
			 10 -> missing or wrong attribute
			100 - 200 -> error while requesting action
		*/
		$this->errorAppend = true;
		return (object) [
			"code"		=> $code,
			"message"	=> $message
		];
	}
	
	public function _generateError($code, $_message = "") {
		switch($code) {
			case 1:
				$message = "problem in query";
			break;
			case 2:
				$message = "error while parsing JSON";
			break;
			case 3:
				$message = "you don't have permission to perform this action";
			break;
			case 10:
				$message = "authentication failed";
			break;
			case 100:
				$message = "problem in script";
			break;
			
				// notifications errors
			case 110:
				$message = "answering the notification failed";
			break;
				// publications error
			case 120:
				$message = "you can't delete publication that is not yours";
			break;
				// owners error
			case 130:
				$message = "you are already own by this user_id";
			break;
				// authentication
			case 140:
				$message = "user authentication failed";
			break;
			
			default:
				$message = "unknown error";
		}
		
		if(strlen($_message) > 0) {
			$message .= " : " . $_message;
		}
		
		echo json_encode((object) [
			"code"		=> $code,
			"message"	=> $message
		]);
		
		exit();
	}


	
/**
	Entity
**/

	public function createEntity($entity_id, $entity_key, $entity_type) { // UPDATED
		$this->mySQL->insert('entity', [$entity_id, $entity_key, $this->_convertEntityType($entity_type, true)]);
	}
	
		private function _convertEntityType($type, $stringToNumber = true) {
			static $_types = [
				["user",	0],
				["object",	1]
			];
			
			if($stringToNumber) { $j = 1; } else { $j = 0; }
				
			foreach($_types as $_type) {
				if($_type[1 - $j] == $type) {
					return $_type[$j];
				}
			}
			
			return $_types[0][$j];
		}
	
		// not used
	public function removeEntity($entity_id) {
		$this->mySQL->query("DELETE FROM entity WHERE entity_id = {entity_id}", ['entity_id' => $entity_id]);
	}
	
	public function getEntity($entity_id) { // UPDATED
		$result = $this->mySQL->select("SELECT * FROM entity WHERE entity_id = {entity_id}", ['entity_id' => $entity_id]);
		
		if(count($result) == 1) {
			$result = $result[0];
			return (object) [
				'id'	=> $result['entity_id'],
				'key'	=> $result['entity_key'],
				'type'	=> $this->_convertEntityType($result['entity_type'], false)
			];
		} else {
			return null;
		}
	}
	
	
/**
	Creation of user
**/

	public function registerUser($user_email, $user_password) { // UPDATED
		$queryResultsForUser = $this->mySQL->select("SELECT * FROM user WHERE user_email = {user_email}", ['user_email' => $user_email]);
		if(count($queryResultsForUser) == 0) {
			return $this->_createUser($user_email, $user_password);
		} else {
			return null;
		}
	}
	
		private function _createUser($user_email, $user_password) { // UPDATED
			$user_id = $this->generateRandomKey()->base64Encode();
			$user_key = $this->generateRandomKey()->base64Encode();

			return $this->_insertUser($user_id, $user_key, $user_email, $user_password);
		}
		
			private function _insertUser($user_id, $user_key, $user_email, $user_password) { // UPDATED
				$password = password_hash($user_password, PASSWORD_DEFAULT);
				$this->createEntity($user_id, $user_key, 'user');
				$this->mySQL->insert('user', [$user_id, $user_email, $password]);
				
				return $this->getUser($user_id);
			}
	
	public function authenticateUser($user_email, $user_password) { // UPDATED
		$queryResultsForUser = $this->mySQL->select("SELECT * FROM user WHERE user_email = {user_email}", ['user_email' => $user_email]);
		if(count($queryResultsForUser) == 1) {
			$queryResultForUser = $queryResultsForUser[0];
			if(password_verify($user_password, $queryResultForUser['user_password'])) {
				return $this->getUser($queryResultForUser['user_id']);
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	
		// not used
	public function removeUser($userID) {
		$this->mySQL->query("DELETE FROM user WHERE ID_user = {userID}", ['userID' => $userID]);
	}

	public function getUser($user_id) { // UPDATED
		$user = $this->getEntity($user_id);
		
		if($user === null || $user->type != 'user') {
			return null;
		} else {
			unset($user->type);
			
			$queryResultForUser = $this->mySQL->select("SELECT * FROM user WHERE user_id = {user_id}", ['user_id' => $user_id])[0];

			$user->email = $queryResultForUser['user_email'];
			
			return $user;
		}
	}
	

	
/**
	Creation of object
**/

	public function registerObject($object_id, $object_key, $object_name, $object_type) { // UPDATED
		if(strlen($object_id) == 44) {
			if(strlen($object_key) == 44) {
				$queryResultsForObject = $this->mySQL->select("SELECT * FROM object WHERE object_id = {object_id}", ['object_id' => $object_id]);
				if(count($queryResultsForObject) == 0) {
					return $this->_createObject($object_id, $object_key, $object_name, $object_type);
				} else {
					return null;
				}
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	
		private function _createObject($object_id, $object_key, $object_name, $object_type) { // UPDATED
			$this->createEntity($object_id, $object_key, 'object');
				
			$this->mySQL->insert('object', [$object_id, $object_name, $object_type]);

			$queryString = "
				CREATE (n1:Object {object_id:{object_id}})
				RETURN n1
			";
				
			$this->grapheneDB->query($queryString, ['object_id' => $object_id]);
			
			return $this->getObject($object_id);
		}

		// not used
	public function removeObject($objectID) {
		$this->mySQL->query("DELETE FROM object WHERE ID_object = {objectID}", ['objectID' => $objectID]);
		
		/*$queryString = "
			MATCH (n1)
			WHERE n1.objectID = {objectID}
			DELETE n1
		";*/
		
		/*MATCH (n1)-[r*0..1]-()
		WHERE n1.objectID = 'Wb+rfDY8aX0r+uxzR3GsYY7TjBQg5eOiwMyJEuOkJu4='
		RETURN n1, r*/

		$queryString = "
			MATCH (n1)-[r]-()
			WHERE n1.objectID = {objectID}
			DELETE n1, r
		";
		
		$this->grapheneDB->query($queryString, ['objectID' => $objectID]);
		
		$queryString = "
			MATCH (n1)
			WHERE n1.objectID = {objectID}
			DELETE n1
		";
		
		$this->grapheneDB->query($queryString, ['objectID' => $objectID]);
	}
	
	public function getObject($object_id) {	// UPDATED
		$object = $this->getEntity($object_id);
		
		if($object === null || $object->type != 'object') {
			return null;
		} else {
			unset($object->type);
			
			$queryResultForObject = $this->mySQL->select("SELECT * FROM object WHERE object_id = {object_id}", ['object_id' => $object_id])[0];

			$object->name = $queryResultForObject['object_name'];
			$object->type = $queryResultForObject['object_type'];
			
			return $object;
		}
	}
	

/**
	Notifications
**/

		private function _createNotification($notification_type, $notification_to_entity, $notification_parameters) { // UPDATED
			$notification_id = $this->generateRandomKey(64)->base64Encode();
			$this->mySQL->insert('notification', [$notification_id, $notification_type, $notification_to_entity, json_encode($notification_parameters), false, $this->getTime()]);
			return $notification_id;
		}
	
	public function getNotifications($notification_to_entity) { // UPDATED
		$notifications = [];
		
		$queryResultsNotifications = $this->mySQL->select(
			"SELECT * FROM notification WHERE notification_to_entity = {notification_to_entity} AND notification_answered = {notification_answered} ORDER BY notification_timestamp DESC LIMIT 100",
			['notification_to_entity' => $notification_to_entity, 'notification_answered' => 0]
		);
		
		foreach($queryResultsNotifications as $queryResultNotification) {
			$notifications[] = $this->_getNotification($queryResultNotification);
		}
		
		return $notifications;
	}
	
		private function _getNotification($queryResultNotification) { // UPDATED
			return (object) [
				'id'				=> $queryResultNotification['notification_id'],
				'type'				=> $queryResultNotification['notification_type'],
				'parameters'		=> json_decode($queryResultNotification['notification_parameters']),
				'timestamp'			=> (double) $queryResultNotification['notification_timestamp']
			];
		}
		
	public function answerNotification($query_from_object_id, $parameters) { // UPDATED
		$queryResultsNotifications = $this->mySQL->select("SELECT * FROM notification WHERE notification_id = {notification_id}", ['notification_id' => $parameters->notification_id]);
		
		if(count($queryResultsNotifications) > 0) {
			$queryResultNotification = $queryResultsNotifications[0];
			
			if(($queryResultNotification['notification_to_entity'] == $query_from_object_id) && !$queryResultNotification['notification_answered']) {
				$notification = $this->_getNotification($queryResultNotification);
				
				//print_r($notification);

				switch($notification->type) {
					
						// owners
					case 'request_for_a_new_owner':
						$owner = $this->_getOwner($notification->parameters->from_object);
						if($owner === null || $owner->user_id != $query_from_object_id) {
							if($parameters->answer) {
								if($owner === null) {
									$this->_createOwnerRelation($notification->parameters->from_object, $query_from_object_id);
								} else {
									$this->_createNotification("request_for_changing_of_owner", $owner->user_id, (object) [
										"new_owner"		=> $query_from_object_id,
										"from_object"	=> $notification->parameters->from_object
									]);
								}
							} else {
								$this->_createNotification("rejection_from_the_new_owner", $notification->parameters->from_object, (object) [
									"from_user"	=> $query_from_object_id
								]);
							}
						}
					break;
					case 'rejection_from_the_new_owner':
						// no response expected
					break;
					case 'rejection_from_the_actual_owner':
						// no response expected
					break;
					case 'have_a_new_owner':
						// no response expected
					break;
					case 'have_a_new_object':
						// no response expected
					break;
					case 'no_more_possess_an_object':
						// no response expected
					break;
					
					case 'request_for_changing_of_owner':
						$owner = $this->_getOwner($notification->parameters->from_object);
						if($owner->user_id == $query_from_object_id) {
							if($parameters->answer) {
								$this->_changeOwner($notification->parameters->from_object, $owner->user_id, $notification->parameters->new_owner);
							} else {
								$this->_createNotification("rejection_from_the_actual_owner", $notification->parameters->new_owner, (object) [
									"new_owner"		=> $notification->parameters->new_owner,
									"actual_owner"	=> $owner->user_id,
								]);
								
								$this->_createNotification("rejection_from_the_actual_owner", $notification->parameters->from_object, (object) [
									"new_owner"		=> $notification->parameters->new_owner,
									"actual_owner"	=> $owner->user_id,
								]);
							};
						} else {
							// can append if object ask for multiple new owners
						}
					break;
					
					
					case 'request_for_a_new_relationship':
						/*
							1) checker les relations existantes
								a) si la relation existe deja, ne rien faire
								b) sinon 2)
							2) creer la relation
						*/
						
						if($parameters->answer) {
							$this->_createRelationshipBetweenObjects($notification->parameters->from_object, $notification->parameters->relationship_name, $query_from_object_id);
							$this->_createNotification("accept_new_relationship", $notification->parameters->from_object, (object) [
								"from_object"		=> $query_from_object_id,
								"relationship_name"	=> $notification->parameters->relationship_name,
							]);
						} else {
							$this->_createNotification("refuse_new_relationship", $notification->parameters->from_object, (object) [
								"from_object"		=> $query_from_object_id,
								"relationship_name"	=> $notification->parameters->relationship_name,
							]);
						};
					break;
					case 'accept_new_relationship':
						// no response expected
					break;
					case 'refuse_new_relationship':
						// no response expected
					break;
					case 'remove_relationship':
						// no response expected
					break;
					default:
						return false;
				}
				
				$this->mySQL->query(
					"UPDATE notification SET notification_answered = {notification_answered} WHERE notification_id = {notification_id}",
					['notification_id' => $parameters->notification_id, 'notification_answered' => 1]
				);
				
				
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	

	
/**
	Publications
**/

	public function postPublication($query_from_object_id, $publication, $to_object_id) { // UPDATED
		$publication = $this->_formatPostedPublication($query_from_object_id, $publication, $to_object_id);
		
		$publication_id = $this->generateRandomKey()->base64Encode();
		
		$this->mySQL->insert('publication', [$publication_id, $query_from_object_id, $to_object_id, $this->getTime()]);
		
		for($i = 0; $i < count($publication->data); $i++) {
			$this->_postData($publication_id, $publication->data[$i]);
		}
		
		return $publication_id;
	}
	
		private function _formatPostedPublication($query_from_object_id, $publication, $to_object_id) { // UPDATED
			for($i = 0, $size = count($publication->data); $i < $size; $i++) {
				$data = $publication->data[$i];
				if($query_from_object_id == $to_object_id) {
					if(count($data->relationships) == 0) {
						$data->relationships = ["public"];	
					}
				} else {
					if(in_array("private", $data->relationships)) {
						$data->relationships = ["private"];
					} else {
						$data->relationships = ["public"];	
					}					
				}
			}
			
			return $publication;
		}
		
		private function _postData($publication_id, $data) { // UPDATED
			$data_id = $this->generateRandomKey()->base64Encode();
			
			$args = [$data_id, $publication_id, $this->_convertDataType($data->type, true)];
			//$argsString = date("Y-m-d H:i:s") . " - " . (floor($this->getTime() / 1000)) . " : [ " . $args[0] . ", " . $args[1] . ", " . $args[2] . " ]\r\n";
			
			$this->mySQL->insert('data', $args);
			
			//file_put_contents('log.txt', $argsString, FILE_APPEND);
			
			$this->dynamoDB->insert('thingbook', [
				new DynamoDBItemAttribute('data_id', $data_id, 'string'),
				new DynamoDBItemAttribute('value', $data->value, 'blob'), // TODO : dont works with objects !
				new DynamoDBItemAttribute('tags', json_encode($data->tags), 'string'),
				new DynamoDBItemAttribute('relationships', json_encode($data->relationships), 'string')
			]);
		}
			
			
	public function getPublication($query_from_object_id, $publication_id) { // UPDATED
		$queryResultsForPublication = $this->mySQL->select("SELECT * FROM publication WHERE publication_id = {publication_id}", ['publication_id' => $publication_id]);
		
		if(count($queryResultsForPublication) == 1) {
			return  $this->_getPublication($query_from_object_id, $queryResultsForPublication[0]);
		} else {
			return null;
		}
	}
	
		private function _getPublication($query_from_object_id, $publication) { // UPDATED
			return $this->_formatGottenPublication($query_from_object_id, (object) [
				'data'				=> $this->_getDataSet($publication['publication_id']),
				'id'				=> $publication['publication_id'],
				'from_object'		=> $publication['from_obj'],
				'to_object'			=> $publication['to_obj'],
				'timestamp'			=> (double) $publication['timestamp']
			]);
		}
		
		private function _formatGottenPublication($query_from_object_id, $publication) { // UPDATED
			if($publication !== null) {
					// if current objectID is sender or receiver, do nothing, else check for relationships
				if(
					!(	$query_from_object_id == $publication->to_object	||
						$query_from_object_id == $publication->from_object)
				) {
						// get the different relationships of the selected object
					$relationships = $this->getRelationships($query_from_object_id, $publication->to_object);
					
					if(isset($relationships[$query_from_object_id])) {
						$relationshipsBetweenObjects = $relationships[$query_from_object_id];
					} else {
						$relationshipsBetweenObjects = null;
					}
						
					//print_r($relationships);
					
						// now, for each data, we mask the value if needed
					for($i = 0, $size = count($publication->data); $i < $size; $i++) {
						$data = $publication->data[$i];
						$allowed = false;
						
						
							// we inspect all relationships allowed
						foreach($data->relationships as $allowedRelationship) {
							if($allowedRelationship == 'public') {
								$allowed = true;
							} else if($relationshipsBetweenObjects !== null) {
								foreach($relationshipsBetweenObjects as $relationship) {
									if($relationship->relationship_name == $allowedRelationship) {
										$allowed = true;
										break;
									}
								}
							}
							
							if($allowed) { break; }
						}
						
						if(!$allowed) {
							unset($publication->data[$i]->value);
							unset($publication->data[$i]->type);
							unset($publication->data[$i]->tags);
						}
					}
				}
			}
			
			return $publication;
	}
		
			private function _getDataSet($publication_id) { // UPDATED
				$dataSet = [];
				$queryResultsForData = $this->mySQL->select("SELECT * FROM data WHERE publication_id = {publication_id}", ['publication_id' => $publication_id]);
				
				foreach($queryResultsForData as $queryResultForData) {
					$dataSet[] = $this->_getData($queryResultForData['data_id'], $queryResultForData['data_type']);
				}
				
				return $dataSet;
			}
			
				private function _getData($data_id, $data_type) { // UPDATED
					$dataValue = $this->dynamoDB->get('thingbook', new DynamoDBItemAttribute('data_id', $data_id, 'string'));
					
					return (object) [
						'value'			=> $dataValue['value']->value,
						'type'			=> $this->_convertDataType($data_type, false),
						'tags'			=> json_decode($dataValue['tags']->value),
						'relationships'	=> json_decode($dataValue['relationships']->value)
					];
				}
			
		private function _convertDataType($type, $stringToNumber = true) {
			static $_types = [
				["raw",		  0],
				["number",	  1],
				["string",	  2],
				["bool",	  3],
				["image",	 10],
				["audio",	 11],
				["video",	 12]
			];
			
			if($stringToNumber) { $j = 1; } else { $j = 0; }
				
			foreach($_types as $_type) {
				if($_type[1 - $j] == $type) {
					return $_type[$j];
				}
			}
			
			return $_types[0][$j];
		}
	
	
		// TODO : bug if no publications
	public function getPublications($query_from_object_id, $parameters) { // UPDATED
		$publications = [];
		$conditions = [];
		$conditions[] = ["to_obj = ?", $parameters->of_object];
		
			// conditions
		if(isset($parameters->before_publication)) {
			$queryResultsForPublication = $this->mySQL->select("SELECT * FROM publication WHERE publication_id = {publication_id}", ['publication_id' => $parameters->before_publication]);
			$conditions[] = ["timestamp < ?", $queryResultsForPublication[0]['timestamp']];
		}
		
		if(isset($parameters->after_publication)) {
			$queryResultsForPublication = $this->mySQL->select("SELECT * FROM publication WHERE publication_id = {publication_id}", ['publication_id' => $parameters->after_publication]);
			$conditions[] = ["timestamp > ?", $queryResultsForPublication[0]['timestamp']];
		}
		
		if(isset($parameters->before_date)) {
			$conditions[] = ["timestamp <= ?", $parameters->before_date];
		}
		
		if(isset($parameters->after_date)) {
			$conditions[] = ["timestamp >= ?", $parameters->after_date];
		}

		
		$query = "SELECT * FROM publication WHERE ";
		$queryParameters = [];
		for($i = 0, $size = count($conditions); $i < $size; $i++) {
			if($i > 0) { $query .= " AND "; }
			
			$query			.= $conditions[$i][0];
			$queryParameters[]	 = $conditions[$i][1];
		}
		
		$query .= " ORDER BY timestamp DESC";
		
		if(!isset($parameters->limit) || !is_numeric($parameters->limit)) { $parameters->limit = 10; }
		$query .= " LIMIT " . min($parameters->limit, 100);
		

		$queryResultsForPublication = $this->mySQL->select($query, $queryParameters);
		
		foreach($queryResultsForPublication as $queryResultForPublication) {
			$publications[] = $this->_getPublication($query_from_object_id, $queryResultForPublication);
		}
		
		return $publications;
	}

	public function removePublication($query_from_object_id, $publication_id) { // UPDATED
		$queryResultsForPublication = $this->mySQL->select("SELECT * FROM publication WHERE publication_id = {publication_id}", ['publication_id' => $publication_id]);
		
		if(count($queryResultsForPublication) == 1) {
			$queryResultForPublication = $queryResultsForPublication[0];
			if(
				$query_from_object_id == $queryResultForPublication['to_obj']	||
				$query_from_object_id == $queryResultForPublication['from_obj']
			) {
				$this->mySQL->query("DELETE FROM publication WHERE publication_id = {publication_id}", ['publication_id' => $publication_id]);
				$this->_removeDataSet($publication_id);
				
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
		private function _removeDataSet($publication_id) { // UPDATED
			$queryResultsForData = $this->mySQL->select("SELECT * FROM data WHERE publication_id = {publication_id}", ['publication_id' => $publication_id]);
			foreach($queryResultsForData as $queryResultForData) {
				$this->_removeData($queryResultForData['data_id']);
			}
		}
		
			private function _removeData($data_id) { // UPDATED
				$this->mySQL->query("DELETE FROM data WHERE data_id = {data_id}", ['data_id' => $data_id]);
				$this->dynamoDB->delete('thingbook', new DynamoDBItemAttribute('data_id', $data_id, 'string'));
			}
	
	
/**
	Owners
**/

		private function _createOwnerRelation($object_id, $user_id) { // UPDATED
			$this->mySQL->insert('owner', [$object_id, $user_id, $this->getTime()]);
			
			$this->_createNotification("have_a_new_owner", $object_id, (object) [
				"new_owner" => $user_id
			]);
			
			$this->_createNotification("have_a_new_object", $user_id, (object) [
				"new_object" => $object_id
			]);
		}
		
		private function _getOwner($object_id) { // UPDATED
			$queryResultsForOwner = $this->mySQL->select("SELECT * FROM owner WHERE object_id = {object_id}", ['object_id' => $object_id]);
		
			if(count($queryResultsForOwner) == 1) {
				$queryResultForOwner = $queryResultsForOwner[0];
				return  (object) [
					'user_id'				=> $queryResultForOwner['user_id'],
					'ownership_timestamp'	=> $queryResultForOwner['timestamp']
				];
			} else {
				return null;
			}
		}
		
		private function _changeOwner($object_id, $old_user_id, $new_user_id) { // UPDATED
			$this->mySQL->query("DELETE FROM owner WHERE object_id = {object_id} AND user_id = {old_user_id}", ['object_id' => $object_id, 'old_user_id' => $old_user_id]);
			
			$this->_createNotification("no_more_possess_an_object", $old_user_id, (object) [
				"new_owner"			=> $new_user_id,
				"released_object"	=> $object_id
			]);
			
			$this->_createOwnerRelation($object_id, $new_user_id);
		}
	
	public function requestForANewOwner($query_from_object_id, $with_user_id) { // UPDATED
		$owner = $this->_getOwner($query_from_object_id);
		if($owner === null || $owner->user_id != $with_user_id) {
			return $this->_createNotification("request_for_a_new_owner", $with_user_id, (object) [
				'from_object'	=> $query_from_object_id
			]);
		} else {
			return false;
		}
	}
	
	public function listOwnedObjects($user_id) { // UPDATED
		$ownedObjects = [];
		
		$queryResultsForOwnedObjects = $this->mySQL->select("SELECT * FROM owner WHERE user_id = {user_id}", ['user_id' => $user_id]);

		foreach($queryResultsForOwnedObjects as $queryResultForOwnedObject) {
			$object = $this->getObject($queryResultForOwnedObject['object_id']);
			$object->ownership_timestamp = $queryResultForOwnedObject['timestamp'];
			$ownedObjects[] = $object;
		}
		
		return $ownedObjects;
	}


/**
	Relationships
**/

	public function requestForANewRelationship($query_from_object_id, $relationship_name, $with_object_id) { // UPDATED
		return $this->_createNotification("request_for_a_new_relationship", $with_object_id, (object) [
			'from_object'		=> $query_from_object_id,
			'relationship_name'	=> $relationship_name
		]);
	}
	
			// TODO : multiple relationships with the same name are possibles!
		public function _createRelationshipBetweenObjects($object_id_1, $relationship_name, $object_id_2) { // UPDATED
			if($this->_relationshipIsValid($relationship_name)) {
				$queryString = "
					MATCH (n1), (n2)
					WHERE n1.object_id = {object_id_1} AND n2.object_id = {object_id_2} AND NOT((n1)-[:" . $relationship_name . "]-(n2))
					CREATE(n1)-[:" . $relationship_name . " { timestamp: {timestamp} }]->(n2)
					RETURN n1, n2
				";
				
				$this->grapheneDB->query($queryString, ['object_id_1' => $object_id_1, 'object_id_2' => $object_id_2, 'timestamp' => $this->getTime()]);
				return true;
			} else {
				return false;
			}
		}
	
			private function _relationshipIsValid($relationship_name) {
				return !preg_match('#[^a-zA-Z0-9]#sU', $relationship_name);
			}
			
	public function removeRelationshipBetweenObjects($query_from_object_id, $relationship_name, $with_object_id) { // UPDATED
		if($this->_relationshipIsValid($relationship_name)) {
			$queryString = "
				MATCH (n1)-[r:" . $relationship_name . "]-(n2)
				WHERE n1.object_id = {object_id_1} AND n2.object_id = {object_id_2}
				DELETE r
			";
			
			$this->grapheneDB->query($queryString, ['object_id_1' => $query_from_object_id, 'object_id_2' => $with_object_id]);
			
			$this->_createNotification("remove_relationship", $with_object_id, (object) [
				"from_object"		=> $query_from_object_id,
				"relationship_name"	=> $relationship_name,
			]);
		
		
			return true;
		} else {
			return false;
		}
	}
	
	public function getRelationships($query_from_object_id, $of_object_id) { // UPDATED
		$queryString = "
			MATCH (n1)-[r]-(n2)
			WHERE n1.object_id = {object_id}
			RETURN n1, r, n2
		";
		
		$result = $this->grapheneDB->query($queryString, ['object_id' => $of_object_id]);
		
		$relationships = [];
		foreach($result as $row) {
			$relationName = $row['r']->getType();
			$object_id_2 = $row['n2']->getProperty('object_id');
			
			if(!isset($relationships[$object_id_2])) { $relationships[$object_id_2] = []; } 
			
			$relationships[$object_id_2][] = (object) [
				"relationship_name"	=> $relationName,
				"timestamp"			=> $row['r']->timestamp
			];
		}
		
		return $relationships;
	}
	

	
	
/**
	Others
**/

	public function generateRandomKey($size = 256) {
		$key = [];
		//openssl_random_pseudo_bytes($size / 8);
		for($i = 0; $i < $size; $i++) {
			$key[$i] = mt_rand(0, 1);
			/*if($key[$i] < 512) {
				$key[$i] = 0;
			} else {
				$key[$i] = 1;
			}*/
		}

		return new BitString($key, 'bits');
	}

	public function getTime() {
		return (double) (microtime(true) * 1000);
	}
	
	
	
		/*** TESTS ***/
	public function clearAll() {
		$this->mySQL->clear();
		$this->grapheneDB->clear();
		$this->dynamoDB->clearTable('thingbook');
	}

	public function initialize() {
	
		$this->clearAll();
		
		for($i = 0; $i < count($this->objectCredentials); $i++) {
			$this->registerObject($this->objectCredentials[$i][0], $this->objectCredentials[$i][1], 'obj' . $i, 'type' . $i);
		}
		
		for($i = 0; $i < count($this->userCredentials); $i++) {
			$this->_insertUser($this->userCredentials[$i][0], $this->userCredentials[$i][1], $this->userCredentials[$i][2], $this->userCredentials[$i][3]);
		}
	}

}

$APIFunctions = new APIFunctions();

set_error_handler(function($code, $message, $file, $line) {
	global $APIFunctions;
	$APIFunctions->_generateError(100, $message . " at line " . $line);
});

?>