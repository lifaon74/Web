<?php

	// concept
class Error {
	public $message;
	public $file;
	public $line;
	
	public function __construct($message, $file, $line) {
		$this->message	= $message;
		$this->file		= $file;
		$this->line	= $line;
	}
	
	public function display() {
		exit("[ERROR] : " . $message . " in " . $file . " on line " . $line);
	}
}

function is_error($var) {
	if(is_object($var) && get_class($var) == "Error") {
		return true;
	} else {
		return false;
	}
}

function arrayToString($array) {
	$string = "";
	for($i = 0, $size_i = count($array); $i < $size_i; $i++) {
		if($i > 0) { $string .= ", "; }
		$string .= $array[$i];
	}
	return $string;
}


function EOL($index = 1) {
	$str = "";
	
	for($i = 0; $i < $index; $i++) {
		$str .= "\r\n";
	}
	
	return $str;
}

function TAB($index = 1, $string = null) {
	if($string === null) {
		$str = "";
		
		for($i = 0; $i < $index; $i++) {
			$str .= "\t";
		}
		
		return $str;
	} else {
		return TAB($index) . preg_replace('#(\r?\n)#isU', "$1" . TAB($index), $string);
	}
}


function numberToByte($number) {
	$byte = "B";
	
	for($i = 7; $i >= 0; $i--) {
		if($number & (1 << $i)) {
			$byte .= '1';
		} else {
			$byte .= '0';
		}
	}
	
	return $byte;
}

function byteToNumber($byte) {
	$number = 0;
	
	for($i = 1; $i < 9; $i++) {
		$number = $number << 1;
		$number |= $byte[$i];
	}
	
	return $number;
}

function is_byte($byte) {
	if(is_string($byte) && (strlen($byte) == 9) && ($byte[0] == "B")) {
		return true;
	} else {
		return false;
	}
}


?>
