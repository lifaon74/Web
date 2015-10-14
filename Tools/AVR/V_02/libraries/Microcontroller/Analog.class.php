<?php 
trait Analog {
	
	protected function analog_init() {
		$this->_registerTryCatchFunction("analog_referenceToReferenceMask");

		$this->_autoCreateTryCatchFunction("analog_resolution", function($bits) {
			switch($bits) {
				case 8:
					return "ADMUX |= B00100000;" . EOL();
				break;
				case 10:
					return "ADMUX &= B11011111;" . EOL();
				break;
				default:
					throw new Exception("Unknown analog precision " . $bits);
			}
		}, [8, 10], false);
		
		$this->_autoCreateTryCatchFunction("analog_prescalerToPrescalerMask", function($prescaler) {
			if($prescaler === floatval(2)) {
				return byteToNumber("B00000001");
			} else if($prescaler === floatval(4)) {
				return byteToNumber("B00000010");
			} else if($prescaler === floatval(8)) {
				return byteToNumber("B00000011");
			} else if($prescaler === floatval(16)) {
				return byteToNumber("B00000100");
			} else if($prescaler === floatval(32)) {
				return byteToNumber("B00000101");
			} else if($prescaler === floatval(64)) {
				return byteToNumber("B00000110");
			} else if($prescaler === floatval(128)) {
				return byteToNumber("B00000111");
			} else {
				throw new Exception("Unknown analog prescaler " . $prescaler);
			}
		}, [2, 4, 8, 16, 32, 64, 128], false);

		$this->_autoCreateTryCatchFunction("analog_pinToPinMask", function($pin) {
			$analogPort = $this->_pinToPinFunction($pin, 'analogPort');
			return $this->constructor->_shiftLeft(1, $analogPort->number);
		}, $this->_pinIndexes, false);
		
		
		$this->_autoCreateTryCatchFunction("analog_read", function($bits) {
			switch($bits) {
				case 8:
					return "ADCH";
				break;
				case 10:
					return "(ADCL | (ADCH << 8))";
				break;
				default:
					throw new Exception("Unknown analog precision " . $bits);
			}
		}, [8, 10], false);
			
	}
	
	
		// analog_referenceToReferenceMask
	public function analog_referenceToReferenceMask($reference) {
		return $this->_callTryCatchFunction("analog_referenceToReferenceMask", $reference);
	}
	
		protected function try_analog_referenceToReferenceMask($reference) {
			if($reference === floatval($this->constructor->DEFAULT)) {
				return byteToNumber("B01000000");
			} else if($reference === floatval($this->constructor->INTERNAL)) {
				return byteToNumber("B11000000");
			} else if($reference === floatval($this->constructor->EXTERNAL)) {
				return byteToNumber("B00000000");
			} else {
				throw new Exception("Unknown analog reference " . $reference . "-" . $this->constructor->DEFAULT);
			}
		}
		
		protected function catch_analog_referenceToReferenceMask($reference) {
			return "analog_referenceToReferenceMask(" . $reference . ")";
		}
	
		protected function code_analog_referenceToReferenceMask() {
			return $this->_AToB_TryCatchFunction("analog_referenceToReferenceMask", ["DEFAULT", "INTERNAL", "EXTERNAL"]);
		}
		
		
		// analog_reference
	public function analog_reference($reference, $mask = false) {
		if(!$mask) { $reference = $this->analog_referenceToReferenceMask($reference); }
		return "ADMUX = (ADMUX & B00111111) | " . $reference . ";" . EOL();
	}
	
		// analog_resolution
	public function analog_resolution($bits) {
		return $this->_callTryCatchFunction("analog_resolution", $bits);
	}
	
	
		// analog_prescalerToPrescalerMask
	public function analog_prescalerToPrescalerMask($prescaler) {
		return $this->_callTryCatchFunction("analog_prescalerToPrescalerMask", $prescaler);
	}
			
		//analog_prescaler
	public function analog_prescaler($prescaler, $mask = false) {
		if(!$mask) { $prescaler = $this->analog_prescalerToPrescalerMask($prescaler); }
		return "ADCSRA = (ADCSRA & B11111000) | " . $prescaler . ";" . EOL();
	}
	
	
		// analog_pinToPinMask
	public function analog_pinToPinMask($pin) {
		return $this->_callTryCatchFunction("analog_pinToPinMask", $pin);
	}
	
		// analog_pin
	public function analog_pin($pin, $mask = false) {
		if(!$mask) { $pin = $this->analog_pinToPinMask($pin); }
		return "ADMUX = (ADMUX & B11110000) | " . $pin . ";" . EOL();
	}

		// analog_startConversion
	public function analog_startConversion() {
		return "ADCSRA |= B01000000;" . EOL();
	}
	
		// analog_conversionStatus
	public function analog_conversionStatus() {
			// true if incomplete
		return "((bool) (ADCSRA & B01000000))";
	}
	
	
		// analog_read
	public function analog_read($bits = 10) {
		return $this->_callTryCatchFunction("analog_read", $bits);
	}
}

?>