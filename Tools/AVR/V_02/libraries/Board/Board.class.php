<?php


require_once(__DIR__ . '/../AlternativeFunctions.class.php');

abstract class ArduinoBoard extends AlternativeFunctions {
	public		$name;
	protected	$constructor;
	public		$microcontroller;
	public		$microcontrollerPinToArduinoPin;

	public function __construct($constructor, $name) {
		$this->constructor	= $constructor;
		$this->name			= $name;
		
		$this->_pinIndexes = [];
		for($i = 0; $i <= 13; $i++) {
			$this->_pinIndexes[] = $i;
		}
			
			
		$this->_initFunctions();
	}
	
		private function _initFunctions() {
			$this->_autoCreateTryCatchFunction("arduinoPinToMicrocontrollerPin", function($pin, $isDigital = true) {
				return $this->pinNameToMicrocontrollerPin($this->pinNumberToPinName($pin, $isDigital));
			}, $this->_pinIndexes, false);
		}
		
	
		// convert a pin number into its full name (ex: 1 => 'D1')
	public function pinNumberToPinName($pinNumber, $isDigital = true) {
		if(is_numeric($pinNumber)) {
			return ($isDigital ? "D" : "A") . $pinNumber;
		} else {
			return $pinNumber;
		}
	}
	
		// convert a pin name into its index for the microcontoller (ex: 'D3' => 0)
	public function pinNameToMicrocontrollerPin($pinName) {
		for($i = 0, $size_i = count($this->microcontrollerPinToArduinoPin); $i < $size_i; $i++) {
			$_pinNames	= $this->microcontrollerPinToArduinoPin[$i];
			for($j = 0, $size_j = count($_pinNames); $j < $size_j; $j++) {
				$_pinName = $_pinNames[$j];
				if($_pinName == $pinName) {
					return $i;
				}
			}
		}
	
		throw new Exception("Pin " . $pinName . " doesn't exist.");
	}
	
	
		// convert an Arduino pin into its index for the microcontoller
	public function arduinoPinToMicrocontrollerPin($pin) {
		return $this->_callTryCatchFunction("arduinoPinToMicrocontrollerPin", $pin);
	}	
			// alias
		public function pin($pin, $isDigital = true) {
			return $this->arduinoPinToMicrocontrollerPin($pin, $isDigital);
		}
	
	
	
	public function begin() {
		$code  = "";
		$code .= $this->microcontroller->analog_resolution(10);
		$code .= $this->microcontroller->analog_prescaler($this->constructor->F_CPU / 1000000);
		return $code;
	}
	
	
	public function pinMode($pin, $mode) {	
		return $this->microcontroller->digital_pinMode($this->pin($pin), $mode);
	}
	
	public function digitalWrite($pin, $state) {	
		return $this->microcontroller->digital_pinWrite($this->pin($pin), $state);
	}
	
	public function digitalRead($pin) {	
		return $this->microcontroller->digital_pinRead($this->pin($pin));
	}
	
	
	public function analogReference($reference) {
		return $this->microcontroller->analog_reference($reference);
	}
	
	public function analogRead($variableName, $pin) {
		$code  = "";
		$code .= $this->microcontroller->analog_pin($this->pin($pin, true));
		$code .= $this->microcontroller->analog_startConversion();
		$code .= "while(" . $this->microcontroller->analog_conversionStatus() . ");" . EOL();
		$code .= $variableName . " = " . $this->microcontroller->analog_read(10) . ";" . EOL();
		
		return $code;
	}
	

}


?>