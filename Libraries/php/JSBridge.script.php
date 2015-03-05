<?php

function sucess($content) {
	$response = (object) array();
	
	$response->status = 'OK';
	$response->content = $content;
	
	echo json_encode($response);
	exit();
}

function error($errorCode, $errorMessage) {
	$response = (object) array();
	
	$response->status = 'ERROR';
	$response->errorCode = $errorCode;
	$response->errorMessage = $errorMessage;
	
	echo json_encode($response);
	exit();
}

function requestAttributes($attributesArray) {
	for($i = 0; $i < count($attributesArray); $i++) {
		if(!isset($_REQUEST[$attributesArray[$i]])) {
			error(11 + $i, 'attribute ' . $attributesArray[$i] . ' missing');
		}
	}
}


if(isset($_REQUEST['ACTION'])) {
	switch($_REQUEST['ACTION']) {
		case 'GET_PAGE':
			requestAttributes(['URL']);
			
			require_once('Form.class.php');
			
			if(preg_match('#^https?\:\/\/#isU', $_REQUEST['URL'])) {
				sucess(file_get_contents($_REQUEST['URL']));
			} else {
				error(21, 'incorrect URL');
			}
		break;
		case 'GEOLOCALISATION/GET_PATH':
			requestAttributes(['ORIGIN', 'DESTINATION']);
			
			require_once('Geolocalisation.class.php');
			
			$origin = json_decode($_REQUEST['ORIGIN']);
			$destination = json_decode($_REQUEST['DESTINATION']);
			
			$path = $geoloc->getPath(
				new Coords($origin[0], $origin[1]),
				new Coords($destination[0], $destination[1])
			);
			
			if($path) {
				sucess($path);
			} else {
				error(21, 'no path found');
			}
		break;
		default:
			error(1, 'no match for attribute ACTION');
	}
} else {
	error(0, 'attribute ACTION missing');
}

error(-1, 'something goes wrong');
?>