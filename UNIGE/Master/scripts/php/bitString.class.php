<?php

class BitString {
	public $bitString;
	public $length;
	
	public function __construct($data, $type = NULL) {
		switch($type) {
			case 'bits':
				$this->bitString = $data;
			break;
			case 'char':
				$this->_convertFromNumber($data, 8);
			break;
			case 'int':
				$this->_convertFromNumber($data, 32);
			break;
			case 'string':
				$this->_convertFromString($data);
			break;
			case 'base64':
				$this->_convertFromString(base64_decode($data));
			break;
			default:
				trigger_error("\"" . $type . "\" is not a valid type");
				exit();
		}
		
		$this->length = count($this->bitString);
	}
	
	private function _reduceBitsArray(&$bitString) {
		for($i = count($bitString) - 1; $i >= 0; $i--) {
			if($bitString[$i]) {
				break;
			} else {
				unset($bitString[$i]);
			}
		}
	}
	
	private function _convertFromNumber($number, $length) {
		$this->bitString = [];
		
		for($i = 0; $i < $length; $i++) {
			$this->bitString[$i] = ($number >> $i) & 0x01;
		}
	}
	
	private function _convertFromString($string) {
		$this->bitString = [];
		
		for($i = 0; $i < strlen($string); $i++) {
			$byte = ord($string[$i]);
			for($j = 0; $j < 8; $j++) {
				$this->bitString[] = ($byte >> $j) & 0x01;
			}
		}
	}

	
	public function getBit($index) {
		if($index < $this->length) {
			return $this->bitString[$index];
		} else {
			return 0;
		}
	}
	
	
	public function toBitString() {
		return $this->bitString;
	}
	
		// not working
	public function toBase($base) {
		$baseEncoded = [];
		$j = 0;
		$number = 0;
		
		return $baseEncoded;
	}
	
	public function toByteString() {
		$byteString = [];
		
		$length = ceil($this->length / 8);
		
		$k = 0;
		for($i = 0; $i < $length; $i++) {
			$byte = 0;
			for($j = 0; $j < 8; $j++) {
			
				if($k < $this->length) {
					$bit = $this->bitString[$k++];
				} else {
					$bit = 0;
				}
				
				$byte |= $bit << $j;
			}
			
			$byteString[] = $byte;
		}
		
		return $byteString;
	}

	public function toCharString() {
		$byteString = $this->toByteString();
		$charString = "";
		
		for($i = 0; $i < count($byteString); $i++) {
			$charString .= chr($byteString[$i]);
		}
		
		return $charString;
	}

	public function base64Encode() {
		return base64_encode($this->toCharString());
	}
}

/*class ByteString {
	public $byteString;
	public $length;
	
	public function __construct($byteString) {
		$this->type = 'byteString';
		$this->byteString = $byteString;
		$this->length = count($this->byteString);
	}
}

class CharString {
	public $charString;
	public $length;
	
	public function __construct($charString) {
		$this->type = 'charString';
		$this->charString = $charString;
		$this->length = strlen($this->charString);
	}
}*/


function BS_or($bitString_1, $bitString_2) {
	$bitString = [];
	$length = max($bitString_1->length, $bitString_2->length);
	
	for($i = 0; $i < $length; $i++) {
		$bitString[$i] = $bitString_1->getBit($i) | $bitString_2->getBit($i);
	}
	
	return new BitString($bitString, 'bits');
}

function BS_and($bitString_1, $bitString_2) {
	$bitString = [];
	$length = max($bitString_1->length, $bitString_2->length);
	
	for($i = 0; $i < $length; $i++) {
		$bitString[$i] = $bitString_1->getBit($i) & $bitString_2->getBit($i);
	}
	
	return new BitString($bitString, 'bits');
}

function BS_xor($bitString_1, $bitString_2) {
	$bitString = [];
	$length = max($bitString_1->length, $bitString_2->length);
	
	for($i = 0; $i < $length; $i++) {
		$bitString[$i] = $bitString_1->getBit($i) ^ $bitString_2->getBit($i);
	}
	
	return new BitString($bitString, 'bits');
}


function BS_shiftRight($bitString_1, $number) {
	$bitString = [];
	for($i = $number; $i < $bitString_1->length; $i++) {
		$bitString[] = $bitString_1->bitString[$i];
	}
	return new BitString($bitString, 'bits');
}

function BS_shiftLeft($bitString_1, $number) {
	$bitString = [];
	
	for($i = 0; $i < $number; $i++) {
		$bitString[] = 0;
	}
	
	for($i = 0; $i < $bitString_1->length; $i++) {
		$bitString[] = $bitString_1->bitString[$i];
	}
	
	return new BitString($bitString, 'bits');
}


function BS_increment($bitString) {
	for($i = 0; $i < $bitString->length; $i++) {
		if($bitString->bitString[$i]) {
			$bitString->bitString[$i] = 0;
		} else {
			break;
		}
	}
	
	$bitString->bitString[$i] = 1;
}

function BS_decrement($bitString) {
	for($i = 0; $i < $bitString->length; $i++) {
		if($bitString->bitString[$i]) {
			break;
		} else {
			$bitString->bitString[$i] = 1;
		}
	}
	
	$bitString->bitString[$i] = 0;
}

function BS_plus($bitString_1, $bitString_2) {
	$bitString = [];
	$remainder = 0; // or carry bit
	
	$length = max($bitString_1->length, $bitString_2->length);
	
	for($i = 0; $i < $length; $i++) {
		$bit_1 = $bitString_1->getBit($i);
		$bit_2 = $bitString_2->getBit($i);
		
		$bitString[]	= $bit_1 ^ $bit_2 ^ $remainder;
		$remainder	= ($bit_1 & $bit_2) | ($bit_2 & $remainder) | ($bit_1 & $remainder);
	}
	
	if($remainder) {
		$bitString[] = $remainder;
	}
	
	return new BitString($bitString, 'bits');
}


function divide($numerator, $denominator) {
	$n = 8;
	$remainder = $numerator;
	$denominator = $denominator << $n;
	
	$bits = [];
	
	for($i = $n - 1; $i >= 0; $i--) {
		$remainder = ($remainder << 1) - $denominator;
		if($remainder >= 0) {
			$bits[] = 1;
		} else {
			$bits[] = 0;
			$remainder += $denominator;
		}
	}
	
	return [$bits, $remainder >> $n];
}

/*$a = new BitString([1, 1, 1, 0], 'bits');
$b = new BitString([1, 1, 1], 'bits');*/
//print_r(BS_and($a, $b));
//print_r(BS_shiftRight($a, 2)->bitString);
//print_r(BS_shiftLeft($a, 2)->bitString);
/*BS_increment($a);
BS_increment($b);*/
/*BS_decrement($a);
BS_decrement($b);
print_r($a);
print_r($b);*/

/*$a = new BitString(89, 'char');
print_r($a->bitString);
print_r($a->toBase(10));*/

//print_r(divide(19, 4)); // = 4R1
?>
