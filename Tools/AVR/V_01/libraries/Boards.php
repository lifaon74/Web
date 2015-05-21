<?php

require_once('Microcontrollers.php');

abstract class ArduinoBoard {
	public $name;
	public $microcontroller;
	public $microcontrollerPinToArduinoPin;
	public $functions, $cppFunctions;
	
	public function __construct($name) {
		$this->name			= $name;
		$this->functions	= [];
		$this->cppFunctions	= "";
	}
	
	
		// convert a pin number into its full name (ex: 1 => 'D1')
	protected function _pinNumberToPinName($pinNumber, $type = 'D') {
		if(is_numeric($pinNumber)) {
			return $type . $pinNumber;
		} else {
			return $pinNumber;
		}
	}
	
		// convert a pin name into its index for the microcontoller (ex: 'D3' => 0)
	protected function _pinNameToPinIndex($pinName) {
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
	
		// convert a pin into its digitalPort object
	protected function _pinToDigitalPort($pin) {
		return $this->_pinToPinFunction($pin, 'digitalPort', 'D');
	}
	
		// convert a pin into its analogPort object
	protected function _pinToAnalogPort($pin) {
		return $this->_pinToPinFunction($pin, 'analogPort', 'A');
	}
	
			// convert a pin into a pinFunction object
		private function _pinToPinFunction($pin, $pinFunctionsType, $type = 'D') {
			$index = $this->_pinNameToPinIndex($this->_pinNumberToPinName($pin, $type));
			
			$returnedPinFunction	= null;
			$pinFunctions			= $this->microcontroller->pinsFunctions[$index];
			
			for($i = 0, $size_i = count($pinFunctions); $i < $size_i; $i++) {
				$pinFunction = $pinFunctions[$i];
				if($pinFunction->type == $pinFunctionsType) {
					$returnedPinFunction = $pinFunction;
					break;
				}
			}
			
			if($returnedPinFunction === null) {
				throw new Exception("Pin " . $pin . " has no " . $pinFunctionsType . ".");
			} else {
				return $returnedPinFunction;
			}
		}

		
		
	public function createFunction($functionName, $try, $catch, $cppFunctions) {
		$this->functions[$functionName] = [
			"try"	=> $try,
			"catch"	=> $catch
		];
		$this->cppFunctions .= $cppFunctions();
	}
	
	public function callFunction() {
		$args = func_get_args();
		return $this->callFunctionNotErrorSensitive($args[0], array_slice($args, 1));
	}
	
		public function callFunctionNotErrorSensitive($functionName, $arguments) {
			try {
				return $this->callFunctionErrorSensitive($functionName, $arguments);
			} catch (Exception $e) {
				return call_user_func_array($this->functions[$functionName]['catch'], $arguments);
			}
		}
		
		public function callFunctionErrorSensitive($functionName, $arguments) {
			return call_user_func_array($this->functions[$functionName]['try'], $arguments);
		}
	
			// create a function converting a pin into something else
		protected function _createPinToFunction($functionName, $microcontrollerFunctionName, $isDigital, $isRef) {
			$this->createFunction($functionName, function($pin) use($microcontrollerFunctionName, $isDigital) {
				return $this->microcontroller->$microcontrollerFunctionName($isDigital ? $this->_pinToDigitalPort($pin) : $this->_pinToAnalogPort($pin));
			}, function($pin) use($functionName, $isRef) {
				return  ($isRef ? "*" : "") . $functionName . "(" . $pin . ")";
			}, function() use($functionName, $isRef) {
				return $this->_pinToB_function($functionName, $isRef);
			});
		}
		

			// generate a cpp function which convert a pin number to something else
		protected function _pinToB_function($functionName, $isRef = false) {
			$pinToB = [];
			for($pin = 0; $pin < 14; $pin++) {
				try {
					$pinToB[$pin] = $this->callFunctionErrorSensitive($functionName, [$pin]);
				} catch (Exception $e) {
				}
			}
			return $this->_AToB_function($functionName, $pinToB, $isRef);
		}
			
				// generate a c function which convert a number to something else
			protected function _AToB_function($functionName, $AToB, $isRef = false) {
				if($isRef) {
					$returnType		= "volatile unsigned char*";
					$reference		= "&";
					$defaultReturn	= "NULL";
				} else {
					$returnType		= "unsigned char";
					$reference		= "";
					$defaultReturn	= "element";
				}
						
				$content  = "";
				
				$content .= TAB(0) . $returnType . " " . $functionName . "(unsigned char element) {" . EOL();
					$content .= TAB(1) . "switch(element) {" . EOL();
						foreach($AToB as $A => $B) {
							//$content .= TAB(2) . "#if defined(" . $B . ")" . EOL();
								$content .= TAB(2) . "case " . $A . ":" . EOL();
									$content .= TAB(3) . "return " . $reference . $B . ";" . EOL();
								$content .= TAB(2) . "break;" . EOL();
							//$content .= TAB(2) . "#endif" . EOL();
								
						}
					$content .= TAB(2) . "default:" . EOL();
						$content .= TAB(3) . "return " . $defaultReturn . ";" . EOL();
					$content .= TAB(2) . "break;" . EOL();
								
					$content .= TAB(1) . "}" . EOL();
					
				$content .= TAB(0) . "}" . EOL();
				
				$content .= EOL();
					
				return $content;
			}
		
		
			

}


class ArduinoProMini extends ArduinoBoard {

	public function __construct() {
		$this->microcontroller = new Atmega328P();
		
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
		
		parent::__construct('ArduinoProMini');
		
		$this->_createPinToFunction('pinToDigitalPortMode', 'digitalPortToDigitalPortIn', true, true);	
		$this->_createPinToFunction('pinToDigitalPortIn', 'digitalPortToDigitalPortIn', true, true);
		$this->_createPinToFunction('pinToDigitalPortOut', 'digitalPortToDigitalPortOut', true, true);
		$this->_createPinToFunction('pinToDigitalPinMask', 'digitalPortToDigitalPinMask', true, false);
		
	
		$this->createFunction('analogReferenceToAnalogReferenceMask', function($reference) {
			return $this->microcontroller->getAnalogReferenceMask($reference);
		}, function($pin) {
			return "analogReferenceToAnalogReferenceMask(" . $pin . ")";
		}, function() {
			return $this->_AToB_function("analogReferenceToAnalogReferenceMask", [
				"DEFAULT"	=> $this->callFunction('analogReferenceToAnalogReferenceMask', "DEFAULT"),
				"INTERNAL"	=> $this->callFunction('analogReferenceToAnalogReferenceMask', "INTERNAL"),
				"EXTERNAL"	=> $this->callFunction('analogReferenceToAnalogReferenceMask', "EXTERNAL")
			]);
		});
		
		$this->createFunction('analogPrescalerToAnalogPrescalerMask', function($prescaler) {
			return $this->microcontroller->getAnalogPrescalerMask($prescaler);
		}, function($prescaler) {
			return "analogPrescalerToAnalogPrescalerMask(" . $prescaler . ")";
		}, function() {
			return $this->_AToB_function("analogPrescalerToAnalogPrescalerMask", [
				"2"		=> $this->callFunction('analogPrescalerToAnalogPrescalerMask', 2),
				"4"		=> $this->callFunction('analogPrescalerToAnalogPrescalerMask', 4),
				"8"		=> $this->callFunction('analogPrescalerToAnalogPrescalerMask', 8),
				"16"	=> $this->callFunction('analogPrescalerToAnalogPrescalerMask', 16),
				"32"	=> $this->callFunction('analogPrescalerToAnalogPrescalerMask', 32),
				"64"	=> $this->callFunction('analogPrescalerToAnalogPrescalerMask', 64),
				"128"	=> $this->callFunction('analogPrescalerToAnalogPrescalerMask', 128)
			]);
		});
		
		$this->_createPinToFunction('pinToAnalogPinMask', 'analogPortToAnalogMask', false, false);

	}
}

?>