<?php

require_once('PinFunctions.php');

abstract class Microconstructor {
	public		$name, $AVRname;
	protected	$constructor;
	public		$frequency;
	public		$pinsFunctions, $numberOfPins;
	
	private		$_functions;
	
	public function __construct($name, $AVRname, $constructor, $frequency) {
		$this->name			= $name;
		$this->AVRname		= $AVRname;
		$this->constructor	= $constructor;
		$this->frequency	= $frequency;
		
		$this->numberOfPins	= count($this->pinsFunctions);
		
		$this->constructor->defineConstant($this->AVRname);
		$this->constructor->defineConstant('F_CPU', $this->frequency);
		
		$this->_functions	= [];
		$this->_pinIndexes	= [];
		
		for($i = 0; $i < $this->numberOfPins; $i++) {
			$this->_pinIndexes[$i] = $i;
		}
		
	}
	
	
		protected function _createTryCatchFunction($functionName, $try, $catch, $equivalentCode) {
			$this->_functions[$functionName] = [
				"try"				=> $try,
				"catch"				=> $catch,
				"equivalentCode"	=> $equivalentCode,
				"included"			=> false
			];
		}
		
		protected function _registerTryCatchFunction($functionName) {
			$this->_createTryCatchFunction(
				$functionName,
				[$this, 'try_' . $functionName],
				[$this, 'catch_' . $functionName],
				[$this, 'code_' . $functionName]
			);
		}
		
		protected function _autoCreateTryCatchFunction($functionName, $try, $options, $isRef) {
			$this->_createTryCatchFunction($functionName, $try, function() use($functionName, $isRef) {
				return ($isRef ? "*" : "") . $functionName . "(" . arrayToString(func_get_args()) . ")";
			}, function() use($functionName, $options, $isRef) {
				return $this->_AToB_TryCatchFunction($functionName, $options, $isRef);
			});
		}
		
		protected function _callTryCatchFunction() {
			$args			= func_get_args();
			$functionName	= $args[0];
			$arguments		= array_slice($args, 1);

			for($i = 0, $size_i = count($arguments); $i < $size_i; $i++) {
				$arguments[$i] = $this->constructor->reduceExpression($arguments[$i]);
			}
			
			return $this->_callTryCatchFunctionErrorInsensitive($functionName, $arguments);
		}
		
			protected function _callTryCatchFunctionErrorInsensitive($functionName, $arguments) {
				try {
					return $this->_callTryCatchFunctionErrorSensitive($functionName, $arguments);
				} catch (Exception $e) {
					if(!$this->_functions[$functionName]['included']) {
						$this->_functions[$functionName]['included'] = true;
						$this->constructor->defineFunction($this->_functions[$functionName]['equivalentCode']());
					}
			
					return call_user_func_array($this->_functions[$functionName]['catch'], $arguments);
				}
			}
			
			protected function _callTryCatchFunctionErrorSensitive($functionName, $arguments) {
				return call_user_func_array($this->_functions[$functionName]['try'], $arguments);
			}
		
		
	
			// generate a cpp function on which convert $options into something else
		protected function _AToB_TryCatchFunction($functionName, $options, $isRef = false) {
			$AToB = [];
			
			for($i = 0, $size_i = count($options); $i < $size_i; $i++) {
				try {
					$AToB[$options[$i]] = $this->_callTryCatchFunctionErrorSensitive($functionName, [$this->constructor->reduceExpression($options[$i])]);
				} catch (Exception $e) {
				}
			}
			return $this->constructor->_AToB_function($functionName, $AToB, $isRef);
		}
			
			// generate a cpp function which convert a pin number into something else
		protected function _pinToB_function($functionName, $isRef = false) {
			return $this->constructor->_AToB_TryCatchFunction($functionName, $this->_pinIndexes, $isRef);
		}
			
			
			// convert a pin into a pinFunction object
		protected function _pinToPinFunction($pin, $pinFunctionsType) {
			if(isset($this->pinsFunctions[$pin])) {
				$pinFunctions = $this->pinsFunctions[$pin];
			} else {
				throw new Exception("Pin " . $pin . " does'nt exist.");
			}
			
			for($i = 0, $size_i = count($pinFunctions); $i < $size_i; $i++) {
				$pinFunction = $pinFunctions[$i];
				if($pinFunction->type == $pinFunctionsType) {
					return $pinFunction;
				}
			}
			
			throw new Exception("Pin " . $pin . " has no " . $pinFunctionsType . ".");
		}
		
	
}

abstract class AutoConfigMicroconstructor extends Microconstructor {

	public function __construct($name, $AVRname, $constructor, $frequency) {
		parent::__construct('Atmega328P', '__AVR_ATmega328P__', $constructor, $frequency);
	}
}	

class Atmega328P extends AutoConfigMicroconstructor {
	
	public function __construct($constructor, $frequency) {
		
		$this->pinsFunctions = [
			[new DigitalPort('D', 3), new InterruptPort(2, 19)], // 1
			[new DigitalPort('D', 4), new InterruptPort(2, 20)], // 2
			[new GND()], // 3
			[new VCC()], // 4
			[new GND()], // 5
			[new VCC()], // 6
			[new DigitalPort('B', 6), new InterruptPort(0, 6)], // 7
			[new DigitalPort('B', 7), new InterruptPort(0, 7)], // 8
			[new DigitalPort('D', 5), new InterruptPort(2, 21)], // 9
			[new DigitalPort('D', 6), new InterruptPort(2, 22)], // 10
			
			[new DigitalPort('D', 7), new InterruptPort(2, 23)], // 11
			[new DigitalPort('B', 0), new InterruptPort(0, 0)], // 12
			[new DigitalPort('B', 1), new InterruptPort(0, 1)], // 13
			[new DigitalPort('B', 2), new InterruptPort(0, 2)], // 14
			[new DigitalPort('B', 3), new InterruptPort(0, 3)], // 15
			[new DigitalPort('B', 4), new InterruptPort(0, 4)], // 16
			[new DigitalPort('B', 5), new InterruptPort(0, 5)], // 17
			[/*AVCC*/], // 18
			[ new AnalogPort(6)], // 19
			[/*AREF*/], // 20
			
			[new GND()], // 21
			[new AnalogPort(7)], // 22
			[new DigitalPort('C', 0), new AnalogPort(0), new InterruptPort(1, 8)], // 23
			[new DigitalPort('C', 1), new AnalogPort(1), new InterruptPort(1, 9)], // 24
			[new DigitalPort('C', 2), new AnalogPort(2), new InterruptPort(1, 10)], // 25
			[new DigitalPort('C', 3), new AnalogPort(3), new InterruptPort(1, 11)], // 26
			[new DigitalPort('C', 4), new AnalogPort(4), new InterruptPort(1, 12)], // 27
			[new DigitalPort('C', 5), new AnalogPort(5), new InterruptPort(1, 13)], // 28
			[new DigitalPort('C', 6), new InterruptPort(1, 14)], // 29
			[new DigitalPort('D', 0), new InterruptPort(2, 16)], // 30
			
			[new DigitalPort('D', 1), new InterruptPort(2, 17)], // 31
			[new DigitalPort('D', 2), new InterruptPort(2, 18)]  // 32
		];
		
		parent::__construct('Atmega328P', '__AVR_ATmega328P__', $constructor, $frequency);
		
		$this->_initFunctions();
	}
	
		private function _initFunctions() {
			
			$this->_autoCreateTryCatchFunction("digital_pinToPortMode", function($pin) {
				$digitalPort = $this->_pinToPinFunction($pin, 'digitalPort');
				return "DDR" . $digitalPort->letter;
			}, $this->_pinIndexes, true);
			
			$this->_autoCreateTryCatchFunction("digital_pinToPortIn", function($pin) {
				$digitalPort = $this->_pinToPinFunction($pin, 'digitalPort');
				return "PIN" . $digitalPort->letter;
			}, $this->_pinIndexes, true);
			
			$this->_autoCreateTryCatchFunction("digital_pinToPortOut", function($pin) {
				$digitalPort = $this->_pinToPinFunction($pin, 'digitalPort');
				return "PORT" . $digitalPort->letter;
			}, $this->_pinIndexes, true);
			
			$this->_autoCreateTryCatchFunction("digital_pinToPinMask", function($pin) {
				$digitalPort = $this->_pinToPinFunction($pin, 'digitalPort');
				return (1 << $digitalPort->number);
			}, $this->_pinIndexes, false);
			
			
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
				return (1 << $analogPort->number);
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
	
		/** 
			Digital
		**/
	
	public function digital_pinToPortMode($pin) {
		return $this->_callTryCatchFunction("digital_pinToPortMode", $pin);
	}
	
	public function digital_pinToPortIn($pin) {
		return $this->_callTryCatchFunction("digital_pinToPortIn", $pin);
	}
	
	public function digital_pinToPortOut($pin) {
		return $this->_callTryCatchFunction("digital_pinToPortOut", $pin);
	}		
	
	public function digital_pinToPinMask($pin) {
		return $this->_callTryCatchFunction("digital_pinToPinMask", $pin);
	}
	
		// mode
	public function digital_pinModeInput($pin) {
		return $this->digital_pinToPortMode($pin) . " &= " . $this->constructor->_invert($this->digital_pinToPinMask($pin)) . ";" . EOL();
	}
	
	public function digital_pinModeOutput($pin) {
		return $this->digital_pinToPortMode($pin) . " |= " . $this->digital_pinToPinMask($pin) . ";" . EOL();
	}
	
	public function digital_pinMode($pin, $mode) {
		return $this->constructor->_if($mode,
			function() use($pin) { return $this->digital_pinModeOutput($pin); },
			function() use($pin) { return $this->digital_pinModeInput($pin); }
		);
	}
	
		// read
	public function digital_pinRead($pin) {
		return "((bool) (" . $this->digital_pinToPortIn($pin) . " & " . $this->digital_pinToPinMask($pin) . "))";
	}
	
		// write
	public function digital_pinWriteLow($pin) {
		return $this->digital_pinToPortOut($pin) . " &= " . $this->constructor->_invert($this->digital_pinToPinMask($pin)) . ";" . EOL();
	}
	
	public function digital_pinWriteHigh($pin) {
		return $this->digital_pinToPortOut($pin) . " |= " . $this->digital_pinToPinMask($pin) . ";" . EOL();
	}
	
	public function digital_pinWrite($pin, $state) {
		return $this->constructor->_if($state,
			function() use($pin) { return $this->digital_pinWriteHigh($pin); },
			function() use($pin) { return $this->digital_pinWriteLow($pin); }
		);
	}
	
	
		/** 
			Analog
		**/
		
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