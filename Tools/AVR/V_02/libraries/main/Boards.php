<?php

require_once('Microcontrollers.php');

abstract class ArduinoBoard {
	public		$name;
	protected	$constructor;
	public		$microcontroller;
	public		$microcontrollerPinToArduinoPin;

	public function __construct($constructor, $name) {
		$this->constructor	= $constructor;
		$this->name			= $name;
		
		$this->_initFunctions();
	}
	
		private function _initFunctions() {
			$AToB = [];
			for($i = 0; $i <= 13; $i++) {
				try {
					$AToB[$i] = $this->pinNameToMicrocontrollerPin($this->pinNumberToPinName($i));
				} catch (Exception $e) {
				}
			}
			$this->constructor->defineFunction($this->constructor->_AToB_function("arduinoPinToMicrocontrollerPin", $AToB, false));

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
	public function pin($pin, $isDigital = true) {
		try {
			return $this->pinNameToMicrocontrollerPin($this->pinNumberToPinName($pin, $isDigital));
		} catch (Exception $e) {
			return "arduinoPinToMicrocontrollerPin(" . $pin . ")";
		}
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


class ArduinoProMini extends ArduinoBoard {

	public function __construct($constructor, $frequency) {
		$this->microcontroller = new Atmega328P($constructor, $frequency);
		
		$this->microcontrollerPinToArduinoPin = [
			['D3'], // 1
			['D4'], // 2
			[], // 3
			[], // 4
			[], // 5
			[], // 6
			[], // 7
			[], // 8
			['D5'], // 9
			['D6'], // 10
			
			['D7'], // 11
			['D8'], // 12
			['D9'], // 13
			['D10'], // 14
			['D11'], // 15
			['D12'], // 16
			['D13'], // 17
			[], // 18
			['A6'], // 19
			[], // 20
			
			[], // 21
			['A7'], // 22
			['A0'], // 23
			['A1'], // 24
			['A2'], // 25
			['A3'], // 26
			['A4'], // 27
			['A5'], // 28
			['RESET'], // 29
			['RX0'], // 30
			
			['TX0'], // 31
			['D2']  // 32
		];
		
		parent::__construct($constructor, 'ArduinoProMini');
	}

}

?>