<?php

class Converter {
	function __construct() {
	}
	
	public function intToByteArray($int) {
		return $this->numberToByteArray($int, 2);
	}
	
	public function byteArrayToInt($byteArray) {
		return $this->byteArrayToNumber($byteArray);
	}
	
	
	public function longToByteArray($long) {
		return $this->numberToByteArray($long, 4);
	}
	
	public function byteArrayToLong($byteArray) {
		return $this->byteArrayToNumber($byteArray);
	}
	
	public function floatToByteArray($float) {
		$bitArray = [];
		for($i = 0; $i < 32; $i++) {
			$bitArray[$i] = 0;
		}
		
		$mantissa	= [];
		$exponent	= 0;
		
		if(is_infinite($float)) {
			for($i = 0; $i < 23; $i++) {
				$mantissa[$i] = 0;
			}
			
			$exponent = 0xFF;
		} else if(is_nan($float)) {
			for($i = 0; $i < 23; $i++) {
				$mantissa[$i] = 0;
			}
			
			$mantissa[22] = 1;
			$exponent = 0xFF;
		} else{
			$number			= abs($float);
			$i				= 0;
			$started		= false;
			$rawMantissa	= [];
				
				// compute rawMantissa
			for($j = 512; $j >= -512; $j--) {
					// div with rest
				$divisor = pow(2, $j);
				$left = intval($number / $divisor);
				$right = $number - ($left * $divisor);
				$number = $right;
				
				if($left >= 1) {
					if(!$started) {
						$exponent = $j;
						$started = true;
					}
					
					$value = 1;
				} else {
					$value = 0;
				}
				
				if($started) {
					$rawMantissa[$i++] = $value;
					if($i >= 24) {
						break;
					}
				}
			}
			
			if($started) {
				for($i = 1; $i < 24; $i++) {
					$mantissa[23 - $i] = $rawMantissa[$i];
				}
				
					// compute exponent
				$exponent += 127;
			} else { // zero
				for($i = 0; $i < 23; $i++) {
					$mantissa[$i] = 0;
				}
				
				$exponent = 0x00;
			}

		}
		
			// mantissa
		for($i = 0; $i < 23; $i++) {
			$bitArray[$i] = $mantissa[$i];
		}
		
			// exponent
		$exponentArr = $this->byteToBitArray($exponent);
		for($i = 0; $i < 8; $i++) {
			$bitArray[23 + $i] = $exponentArr[$i];
		}
		
			// negative
		if($float < 0) {
			$bitArray[31] = 1;
		} else {
			$bitArray[31] = 0;
		}
		
		
		return $this->bitArrayToByteArray($bitArray);
	}
	
	public function byteArrayToFloat($byteArray) {
		$bitArray = $this->byteArrayToBitArray($byteArray);
		
		//print_r($bitArray);
		
			// is negative ?
		$negative = $bitArray[31];
		
			// exponent
		$exponentArr = [];
		for($i = 0; $i < 8; $i++) {
			$exponentArr[$i] = $bitArray[23 + $i];
		}
		$exponent = $this->bitArrayToByte($exponentArr);
		
			// mantissa
		$mantissa = [];
		for($i = 0; $i < 23; $i++) {
			$mantissa[$i] = $bitArray[$i];
		}
		
		
		if($exponent == 0xFF) {
			for($i = 0; $i < 23; $i++) {
				if($mantissa[$i]) {
					break;
				}
			}
			if($i == 23) {	// full of zeros
				$float = INF;
			} else {
				$float = NAN;
			}
		} else {
		
				// compute exponent
			$exponent -= 127;
			
				// compute mantissa
			$float = 1.0;
			for($i = 0; $i < 23; $i++) {
				if($mantissa[22 - $i]) {
					$float += pow(2, -($i + 1));
				}
			}
			
			if($float == 1.0) {	// zero
				$float = 0;
			} else {
				$float *= pow(2, $exponent);
			}
		}
		
		if($negative) {
			$float = -$float;
		}
		
		return $float;
	}
	
	
		// Little endian
	public function numberToByteArray($number, $size) {
		$byteArray = [];
		
		for($i = 0; $i < $size; $i++) {
			$byteArray[$i] = ($number >> ($i * 8)) & 0xFF;
		}
		
		return $byteArray;
	}
	
	public function byteArrayToNumber($byteArray) {
		$number = 0;
		
		for($i = 0; $i < count($byteArray); $i++) {
			$number |= $byteArray[$i] << ($i * 8);
		}
		
		return $number;
	}
	
	public function byteToBitArray($byte) {
		$bitArray = [];
		
		for($i = 0; $i < 8; $i++) {
			$bitArray[$i] = ($byte >> $i) & 0x01;
		}
		
		return $bitArray;
	}
	
	public function bitArrayToByte($bitArray) {
		$byte = 0;
		
		for($i = 0; $i < 8; $i++) {
			$byte |= $bitArray[$i] << $i;
			
		}
		
		return $byte;
	}
	
	public function byteArrayToBitArray($byteArray) {
		$bitArray = [];
		
		$k = 0;
		for($i = 0; $i < count($byteArray); $i++) {
			$array = $this->byteToBitArray($byteArray[$i]);
			for($j = 0; $j < 8; $j++) {
				$bitArray[] = $array[$j];
			}
		}
		
		return $bitArray;
	}
	
	public function bitArrayToByteArray($bitArray) {
		$byteArray = [];
		
		$k = 0;
		for($i = 0; $i < count($bitArray) / 8; $i++) {
			$subBitArray = [];
			for($j = 0; $j < 8; $j++) {
				$subBitArray[$j] = $bitArray[$i * 8 + $j];
			}
			
			$byteArray[] = $this->bitArrayToByte($subBitArray);
		}
		
		return $byteArray;
	}
	
	
	public function stringToChar($string) {
		$char = [];
		for($i = 0; $i < strlen($string); $i++) {
			$char[$i] = ord($string[$i]);
		}
		return $char;
	}
	
	public function charToString($char) {
		$string = "";
		for($i = 0; $i < count($char); $i++) {
			$string .= chr($char[$i] % 256);
		}
		return $string;
	}
	
	
	public function dateTimeToTimestamp($date) {
		$date = str_replace(array(' ', ':'), '-', $date);
		$c    = explode('-', $date);
		$c    = array_pad($c, 6, 0);
		array_walk($c, 'intval');
		
		return mktime($c[3], $c[4], $c[5], $c[1], $c[2], $c[0]);
	}
}

$Converter = new Converter();
?>