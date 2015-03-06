<?php
header("Content-Type:text/plain");

//print_r($_REQUEST);

function callAPI($json) {
	return json_decode(file_get_contents("http://78.244.106.44/Web/UNIGE/Master/api/api.php" . "?query=" . urlencode($json)));
}

if(isset($_REQUEST['action'])) {
	$id			= "h2PHih+OnjMtM1GJF49y6eKQ2LnvQA4deUeXz/9Rpmo=";
	$key		= "6YjWXyITO+LHoLiyJ/9QZ7FXuIKBHhShP576pX52CJM=";
	
	$firendId	= "JKlX1OMExkyo4qksqIqUUvYGIrGGQHZQKI6FOR8PJ00=";
	
	switch($_REQUEST['action']) {
		case "register":
		
			$json = '
				{
					"action"		: "register_object",
					"parameters"	: {
						"id"	: "' . $id . '",
						"key"	: "' . $key . '",
						"name"	: "electrical outlet",
						"type"	: "electrical outlet"
					}
				}
			';
			
			$response = callAPI($json);
			if($response->code == 0) {
				echo '0';
			} else {
				echo '1';
			}
		break;
		
		case "ask_relationship":
			$json = '
				{
					"id"			: "' . $id . '",
					"key"			: "' . $key . '",
					"action"		: "request_for_a_new_relationship",
					"parameters"	: {
						"relationship"	: "friend",
						"with_object"	: "' . $firendId . '"
					}
				}
			';
			
			$response = callAPI($json);
			if($response->code == 0) {
				echo '0';
			} else {
				echo '1';
			}
		break;
		
		case "is_friend":
			
			$json = '
				{
					"id"			: "' . $id . '",
					"key"			: "' . $key . '",
					"action"		: "get_relationships",
					"parameters"	: {
						"of_object"	: "' . $id . '"
					}
				}
			';
			
			$response = callAPI($json);
			
			$isFriend = false;
			
			if($response->code == 0) {
				if(isset($response->response->relationships->$firendId)) {
					$relationships = $response->response->relationships->$firendId;
					
					for($i = 0; $i < count($relationships); $i++) {
						$relationship = $relationships[$i];
						if($relationship->relationship_name == "friend") {
							$isFriend = true;
							break;
						}
					}
				}
			}
			
			if($isFriend) {
				echo '1';
			} else {
				echo '0';
			}
		break;
		
		case "switch_is_on":
			$json = '
				{
					"id"			: "' . $id . '",
					"key"			: "' . $key . '",
					"action"		: "get_publications",
					"parameters"	: {
						"of_object"				: "' . $firendId . '",
						"limit"					: 1
					}
				}
			';
			
			$response = callAPI($json);
			$switchOn = false; 
			if(count($response->response->publications) > 0) {
				$publication = $response->response->publications[0];
				foreach($publication->data as $data) {
					if(isset($data->value)) {
						foreach($data->tags as $tag) {
							if($tag == "switch") {
								$switchOn = $data->value;
							}
						}
					}
				}
			}
			
			if($switchOn) {
				echo '1';
			} else {
				echo '0';
			}
		break;
		
	}
}
?>