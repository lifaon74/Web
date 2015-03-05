<?php

class Form {
	private $fields = [];
	
	function __construct() {
	}
	
	public function addField($name, $value, $isFile = false, $mimetype = 'image/png') {
		$field = (object) array();
		
		$field->name = $name;
		$field->value = $value;
		$field->isFile = $isFile;
		$field->mimetype = $mimetype;
		
		$this->fields[$field->name] = $field;
	}
	
	public function clear() {
		$this->fields = [];
	}
	
	public function post($url) {
		$MULTIPART_BOUNDARY = '--------------------------' . microtime(true);
		
		$header = 'Content-Type: multipart/form-data; boundary=' . $MULTIPART_BOUNDARY;
		$content = "";
		
		foreach($this->fields as $name => $field) {
			if($field->isFile) {
				$fileContents = file_get_contents($field->value);
				
				$content .=	"--" . $MULTIPART_BOUNDARY . "\r\n" .
							"Content-Disposition: form-data; name=\"" . $field->name . "\"; filename=\"" . basename($field->value) . "\"\r\n".
							"Content-Type: " . $field->mimetype . "\r\n\r\n".
							$fileContents . "\r\n";
					
			} else {
				$content .=	"--" . $MULTIPART_BOUNDARY."\r\n".
							"Content-Disposition: form-data; name=\"" . $field->name . "\"\r\n\r\n".
							$field->value . "\r\n";
			}
		}
		
		$content .= "--" . $MULTIPART_BOUNDARY . "--\r\n";
		
		$context = stream_context_create(
			array(
				'http' => array( 
					'method'	=> 	'POST',
					'follow_location' => false,
					'header'	=>	$header,
					'content'	=> 	$content
				)
			)
		);
		
		$response = file_get_contents($url, false, $context);
		
			// convert header
		$responseHeader = array();
		preg_match('#^HTTP\/[0-9\.]+ ([0-9]+) [a-z]+$#isU', $http_response_header[0], $matches);
		$responseHeader['status'] = $matches[1];
		
		for($i = 1; $i < count($http_response_header); $i++) {
			preg_match('#^([^\:]+): (.+)$#isU', $http_response_header[$i], $matches);
			$responseHeader[$matches[1]] = $matches[2];
		}
		
		
		$return = (object) array(
			'header' => $responseHeader,
			'content' => $response
		);
		
		return $return;
	}

}
?>