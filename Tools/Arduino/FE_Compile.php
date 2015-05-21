<?php

/*$microcontrollerPinToArduinoPin = [
	[], // 1
	[], // 2
	[], // 3
	[], // 4
	[], // 5
	[], // 6
	[], // 7
	[], // 8
	[], // 9
	[], // 10
	
	[], // 11
	[], // 12
	[], // 13
	[], // 14
	[], // 15
	[], // 16
	[], // 17
	[], // 18
	[], // 19
	[], // 20
	
	[], // 21
	[], // 22
	[], // 23
	[], // 24
	[], // 25
	[], // 26
	[], // 27
	[], // 28
	[], // 29
	[], // 30
	
	[], // 31
	[], // 32
	[], // 33
	[], // 34
	[], // 35
	[], // 36
	[], // 37
	[], // 38
	[], // 39
	[], // 40
	
	[], // 41
	[], // 42
	[], // 43
	[] // 44
];*/


function EOL($index = 1) {
	$str = "";
	
	for($i = 0; $i < $index; $i++) {
		$str .= "\r\n";
	}
	
	return $str;
}

function TAB($index = 1) {
	$str = "";
	
	for($i = 0; $i < $index; $i++) {
		$str .= "\t";
	}
	
	return $str;
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



class DigitalPort {
	public $type;
	public $letter, $number;

	function __construct($letter, $number) {
		$this->type		= 'digitalPort';
		$this->letter	= $letter;
		$this->number	= $number;
	}
	
}

class AnalogPort {
	public $type;
	public $number;
	
	function __construct( $number) {
		$this->type		= 'analogPort';
		$this->number	= $number;
	}
	
}

class InterruptPort {
	public $type;
	public $number, $group;

	function __construct($group, $number) {
		$this->type		= 'interruptPort';
		$this->group	= $group;
		$this->number	= $number;
	}
	
}


class Microcontroller {
	public $name;
	public $AVRname;
	public $numberOfPin;
	public $pinsFunctions;
	public $interruptPortGroupsPins;
	public $ports;
	
	function __construct() {
		$this->ports = $this->_getAvailablePortsLetters();
	}

	public function getDigitalPortMask($digitalPort) {
		return numberToByte(1 << $digitalPort->number);
	}

	
	public function getAnalogPortMask($analogPort) {
		switch($analogPort->number) {
			case 0:
				return numberToByte(0b00000000);
			break;
			case 1:
				return numberToByte(0b00000001);
			break;
			case 4:
				return numberToByte(0b00000100);
			break;
			case 5:
				return numberToByte(0b00000101);
			break;
			case 6:
				return numberToByte(0b00000110);
			break;
			case 7:
				return numberToByte(0b00000111);
			break;
			
			case 8:
				return numberToByte(0b00100000);
			break;
			case 9:
				return numberToByte(0b00100001);
			break;
			case 10:
				return numberToByte(0b00100010);
			break;
			case 11:
				return numberToByte(0b00100011);
			break;
			case 12:
				return numberToByte(0b00100100);
			break;
			case 13:
				return numberToByte(0b00100101);
			break;
		}
	}

	
	public function getInterruptPortGroup($interruptPort) {
		return numberToByte(1 << $interruptPort->group);
	}
	
	public function getInterruptPortMask($interruptPort) {
		$groupPins = $this->interruptPortGroupsPins[$interruptPort->group];
		
		for($i = 0, $size = count($groupPins); $i < $size; $i++) {
			if($groupPins[$i] == $interruptPort->number) {
				return numberToByte(1 << $i);
			}
		}
	}

	
	
	private function _getAvailablePortsLetters() {
		$ports = [];
		
		for($i = 0; $i < count($this->pinsFunctions); $i++) {
			for($j = 0; $j < count($this->pinsFunctions[$i]); $j++) {
				$pinFunction = $this->pinsFunctions[$i][$j];
				
				if($pinFunction->type == 'digitalPort') {
					if(array_search($pinFunction->letter, $ports) === false) {
						$ports[] = $pinFunction->letter;
					}
				}
			}
		}
		
		sort($ports);
		
		return $ports;
	}
	
}

class Atmega32U4 extends Microcontroller {
	
	function __construct() {
		$this->name = 'atmega32U4';
		$this->AVRname = '__AVR_ATmega32U4__';
		
		$this->numberOfPin = 44;
		$this->pinsFunctions = [
			[new DigitalPort('E', 6)], // 1
			[], // 2
			[], // 3
			[], // 4
			[], // 5
			[], // 6
			[], // 7
			[new DigitalPort('B', 0), new InterruptPort(0, 0)], // 8
			[new DigitalPort('B', 1), new InterruptPort(0, 1)], // 9
			[new DigitalPort('B', 2), new InterruptPort(0, 2)], // 10
			
			[new DigitalPort('B', 3), new InterruptPort(0, 3)], // 11
			[new DigitalPort('B', 7), new InterruptPort(0, 7)], // 12
			[], // 13
			[], // 14
			[], // 15
			[], // 16
			[], // 17
			[new DigitalPort('D', 0)], // 18
			[new DigitalPort('D', 1)], // 19
			[new DigitalPort('D', 2)], // 20
			
			[new DigitalPort('D', 3)], // 21
			[], // 22
			[], // 23
			[], // 24
			[new DigitalPort('D', 4), new AnalogPort(8)], // 25
			[new DigitalPort('D', 6), new AnalogPort(9)], // 26
			[new DigitalPort('D', 7), new AnalogPort(10)], // 27
			[new DigitalPort('B', 4), new AnalogPort(11), new InterruptPort(0, 4)], // 28
			[new DigitalPort('B', 5), new AnalogPort(12), new InterruptPort(0, 5)], // 29
			[new DigitalPort('B', 6), new AnalogPort(13), new InterruptPort(0, 6)], // 30
			
			[new DigitalPort('C', 6)], // 31
			[new DigitalPort('C', 7)], // 32
			[new DigitalPort('E', 2)], // 33
			[], // 34
			[], // 35
			[new DigitalPort('F', 7), new AnalogPort(7)], // 36
			[new DigitalPort('F', 6), new AnalogPort(6)], // 37
			[new DigitalPort('F', 5), new AnalogPort(5)], // 38
			[new DigitalPort('F', 4), new AnalogPort(4)], // 39
			[new DigitalPort('F', 1), new AnalogPort(1)], // 40
			
			[new DigitalPort('F', 0), new AnalogPort(0)], // 41
			[], // 42
			[], // 43
			[] // 44
		];
		
		$this->interruptPortGroupsPins = [
			"0"	=> [ 0,  1,  2,  3,  4,  5,  6,  7]
		];
		
		parent::__construct();
	}

}

class Atmega328P_32MLF extends Microcontroller {
	
	
	function __construct() {
		$this->name = 'Atmega328P';
		$this->AVRname = '__AVR_ATmega328P__';
		
		$this->numberOfPin = 32;
		
		$this->pinsFunctions = [
			[new DigitalPort('D', 3), new InterruptPort(2, 19)], // 1
			[new DigitalPort('D', 4), new InterruptPort(2, 20)], // 2
			[], // 3
			[], // 4
			[], // 5
			[], // 6
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
			[], // 18
			[ new AnalogPort(6)], // 19
			[], // 20
			
			[], // 21
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
		
		$this->interruptPortGroupsPins = [
			"0"	=> [ 0,  1,  2,  3,  4,  5,  6,  7],
			"1"	=> [ 8,  9, 10, 11, 12, 13, 14],
			"2"	=> [16, 17, 18, 19, 20, 21, 22, 23]
		];
		
		parent::__construct();
	}

}

class ATtiny85 extends Microcontroller {
	
	
	function __construct() {
		$this->name = 'ATtiny85';
		$this->AVRname = '__AVR_ATtiny85__';
		
		$this->numberOfPin = 32;
		
		$this->pinsFunctions = [
			[new DigitalPort('B', 5)], // 1
			[new DigitalPort('B', 3)], // 2
			[new DigitalPort('B', 4)], // 3
			[], // 4
			[new DigitalPort('B', 0)], // 5
			[new DigitalPort('B', 1)], // 6
			[new DigitalPort('B', 2)], // 7
			[] // 8
		];
		
		$this->interruptPortGroupsPins = [];
		
		parent::__construct();
	}

}

class ArduinoCard {
	public $name;
	public $microcontroller;
	public $microcontrollerPinToArduinoPin;
	
	function __construct() {
	}
	
	public function getUppercaseCardName() {
		$cardName = preg_replace("#([A-Z]+)#sU", "_$1", $this->name);
		$cardName = preg_replace("#^_#sU", "", strtoupper($cardName));
		return $cardName;
	}
}

class ArduinoProMicro extends ArduinoCard {

	function __construct() {
		$this->name = 'ArduinoProMicro';
		$this->microcontroller = new Atmega32U4();
		
		$this->microcontrollerPinToArduinoPin = [
			['D7'], // 1
			[], // 2
			[], // 3
			[], // 4
			[], // 5
			[], // 6
			[], // 7
			[], // 8
			['D15'], // 9
			['D16'], // 10
			
			['D14'], // 11
			[], // 12
			[], // 13
			[], // 14
			[], // 15
			[], // 16
			[], // 17
			['D3'], // 18
			['D2'], // 19
			['RX0'], // 20
			
			['TX0'], // 21
			[], // 22
			[], // 23
			[], // 24
			['D4'], // 25
			[], // 26
			['D6'], // 27
			['D8'], // 28
			['D9'], // 29
			['D10'], // 30
			
			['D5'], // 31
			[], // 32
			[], // 33
			[], // 34
			[], // 35
			['A0'], // 36
			['A1'], // 37
			['A2'], // 38
			['A3'], // 39
			[], // 40
			
			[], // 41
			[], // 42
			[], // 43
			[] // 44
		];
	}

}

class ArduinoYun extends ArduinoCard {

	function __construct() {
		$this->name = 'ArduinoYun';
		$this->microcontroller = new Atmega32U4();
		
		$this->microcontrollerPinToArduinoPin = [
			['D7'], // 1
			[], // 2
			[], // 3
			[], // 4
			[], // 5
			[], // 6
			[], // 7
			[], // 8
			['ICSP2'], // 9
			['ICSP3'], // 10
			
			['ICSP0'], // 11
			['D11'], // 12
			[], // 13
			[], // 14
			[], // 15
			[], // 16
			[], // 17
			['D3'], // 18
			['D2'], // 19
			['RX0', 'D0'], // 20
			
			['TX0', 'D1'], // 21
			[], // 22
			[], // 23
			[], // 24
			['D4'], // 25
			['D12'], // 26
			['D6'], // 27
			['D8'], // 28
			['D9'], // 29
			['D10'], // 30
			
			['D5'], // 31
			['D13'], // 32
			[], // 33
			[], // 34
			[], // 35
			['A0'], // 36
			['A1'], // 37
			['A2'], // 38
			['A3'], // 39
			['A4'], // 40
			
			['A5'], // 41
			[], // 42
			[], // 43
			[]  // 44
		];
	}

}

class ArduinoProMini extends ArduinoCard {

	function __construct() {
		$this->name = 'ArduinoProMini';
		$this->microcontroller = new Atmega328P_32MLF();
		
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
	}

}

class ArduinoNano extends ArduinoCard {

	function __construct() {
		$this->name = 'ArduinoNano';
		$this->microcontroller = new Atmega328P_32MLF();
		
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
	}

}

class ArduinoTiny extends ArduinoCard {

	function __construct() {
		$this->name = 'ArduinoTiny';
		$this->microcontroller = new ATtiny85();
		
		$this->microcontrollerPinToArduinoPin = [
			['D0'], // 1
			['D1'], // 2
			['D2'], // 3
			[], // 4
			['D3'], // 5
			['D4'], // 6
			['D5'], // 7
			[] // 8
		];
	}

}


class FE_compiler {
	private $_libName, $_define;
	public $compiledPath;
	
	function __construct($compiledPath) {
		$this->compiledPath	= $compiledPath;
		
		$this->_libName		= "FE";
		$this->_define		= "#define";
	}
	
	
	
	public function compileFor($arduinoCards) {
		foreach($arduinoCards as $arduinoCard) {
			$this->_saveContent('PINMAP/' . $arduinoCard->getUppercaseCardName() . '_PINMAP', $this->compileFor_PINMAP($arduinoCard));
		}
		
		$this->_saveContent('FE_TO', $this->compileFor_TO());
	}
	
		private function _saveContent($path, $content) {
			/*echo $content;
			exit();*/
			
			$fileName = $this->compiledPath . $path;
			file_put_contents($fileName . '.h', $content);
			file_put_contents($fileName . '.cpp', "");
		}
	
	
	public function compileFor_PINMAP($arduinoCard) {
		
		$content = "";

		$content .= $this->_generateHeader($arduinoCard);
		
		$cardName = $arduinoCard->getUppercaseCardName();
		
		$content .= "#ifndef " . $cardName . "_PINMAP_H" . EOL();
		$content .= "#define " . $cardName . "_PINMAP_H" . EOL(2);
		
			$content .= TAB() . "#if defined(" . $arduinoCard->microcontroller->AVRname . ")" . EOL(2);
			
				$content .= $this->_generatePinMap($arduinoCard);
			
			$content .= TAB() . "#endif";
		
		$content .= EOL();
		$content .= "#endif";

		return $content;
	}

		private function _generateHeader($arduinoCard) {
			$content = "";
			
			$content .= "/*" . EOL();
				$content .= TAB() . $arduinoCard->name . " ports : " . EOL();
				

				$ports = [];
				for($i = 0, $size = count($arduinoCard->microcontroller->ports); $i < $size; $i++) {
					$ports[$arduinoCard->microcontroller->ports[$i]] = [];
				}

				
				for($i = 0; $i < count($arduinoCard->microcontroller->pinsFunctions); $i++) {
					for($j = 0; $j < count($arduinoCard->microcontroller->pinsFunctions[$i]); $j++) {
						$pinFunction = $arduinoCard->microcontroller->pinsFunctions[$i][$j];
						
						if($pinFunction->type == 'digitalPort') {
							$ports[$pinFunction->letter][$pinFunction->number] = $arduinoCard->microcontrollerPinToArduinoPin[$i];
						}
					}
				}
				
				foreach($ports as $letter => $port) {
					$line = "";
					for($i = 0; $i < 8; $i++) {
						if($i > 0) { $line .= ","; }
						$str = "";
						
						if(array_key_exists($i, $port) && (count($port[$i]) > 0)) {
							$str .= "[";
								for($j = 0; $j < count($port[$i]); $j++) {
									if($j > 0) { $str .= ", "; }
									$str .= $port[$i][$j];
								}
							$str .= "]";
						} else {
							$str .= "*";
						}
						
						for($j = 0; $j < 12 - strlen($str); $j++) {
							$line .= " ";
						}
						
						$line .= $str;
							
					}
				
					$content .= TAB(2) . "PORT" . $letter . " : " . $line . EOL();
				}

				
			$content .= "*/" . EOL(2);
			
			return $content;
		}
		
		private function _generatePinMap($arduinoCard) {
			$content = "";
			
			for($i = 0; $i < count($arduinoCard->microcontrollerPinToArduinoPin); $i++) { // for each microcontroller pins
				for($j = 0; $j < count($arduinoCard->microcontrollerPinToArduinoPin[$i]); $j++) {
				
					$arduinoPinName = $arduinoCard->microcontrollerPinToArduinoPin[$i][$j];
					
					for($k = 0; $k < count($arduinoCard->microcontroller->pinsFunctions[$i]); $k++) {
						$pinFunction = $arduinoCard->microcontroller->pinsFunctions[$i][$k];
						
						switch($pinFunction->type) {
							case 'digitalPort':
								$content .= TAB(2) . $this->_define . " " . $this->_getConstantName(["DIGITAL", "PIN", $arduinoPinName, "MASK"]) . " " . $arduinoCard->microcontroller->getDigitalPortMask($pinFunction) . EOL();
								$content .= TAB(2) . $this->_define . " " . $this->_getConstantName(["DIGITAL", "PIN", $arduinoPinName, "PORT_MODE"]) . " " . "DDR" . $pinFunction->letter . EOL();
								$content .= TAB(2) . $this->_define . " " . $this->_getConstantName(["DIGITAL", "PIN", $arduinoPinName, "PORT_IN"]) . " " . "PIN" . $pinFunction->letter . EOL();
								$content .= TAB(2) . $this->_define . " " . $this->_getConstantName(["DIGITAL", "PIN", $arduinoPinName, "PORT_OUT"]) . " " . "PORT" . $pinFunction->letter . EOL();
								$content .= TAB(2) . $this->_define . " " . $this->_getConstantName(["DIGITAL", "PIN", $arduinoPinName, "PORT_NUMBER"]) . " " . (ord($pinFunction->letter) - 65) . EOL();
							break;
							
							case 'analogPort':
								$content .= TAB(2) . $this->_define . " " . $this->_getConstantName(["ANALOG", "PIN", $arduinoPinName, "MASK"]) . " " . $arduinoCard->microcontroller->getAnalogPortMask($pinFunction) . EOL();
							break;
							
							case 'interruptPort':
								$content .= TAB(2) . $this->_define . " " . $this->_getConstantName(["INTERRUPT", "PIN", $arduinoPinName, "GROUP_MASK"]) . " " . $arduinoCard->microcontroller->getInterruptPortGroup($pinFunction) . EOL();
								$content .= TAB(2) . $this->_define . " " . $this->_getConstantName(["INTERRUPT", "PIN", $arduinoPinName, "GROUP"]) . " " . "PCMSK" . $pinFunction->group . EOL();
								$content .= TAB(2) . $this->_define . " " . $this->_getConstantName(["INTERRUPT", "PIN", $arduinoPinName, "GROUP_NUMBER"]) . " " . $pinFunction->group . EOL();
								$content .= TAB(2) . $this->_define . " " . $this->_getConstantName(["INTERRUPT", "PIN", $arduinoPinName, "MASK"]) . " " . $arduinoCard->microcontroller->getInterruptPortMask($pinFunction) . EOL();
							break;
						}
						
					}
					
					$content .= EOL();
				}
			}
			
			return $content;
		}
		
		
	public function compileFor_TO() {
		$content = "";
		
		$content .= "#ifndef " . "FE_TO_H" . EOL();
		$content .= "#define " . "FE_TO_H" . EOL(2);
		
			$content .= $this->_TO_defineConstants() . EOL(2);
			
			$portNames = [0, 1, 2, 3, 4];
			$content .= $this->_TO_function("portTo", ["DIGITAL", "PORT", NULL, "MODE"], $portNames , true);
			$content .= $this->_TO_function("portTo", ["DIGITAL", "PORT", NULL, "IN"], $portNames , true);
			$content .= $this->_TO_function("portTo", ["DIGITAL", "PORT", NULL, "OUT"], $portNames , true);
			
			$content .= EOL();
			
			
			$content .= $this->_TO_function("groupTo", ["INTERRUPT", "GROUP", NULL], [0, 1, 2], true);
			
			$content .= EOL();
			
			
			$pinNames = [];
			for($i = 0, $size = 16; $i < $size; $i++) {
				$pinNames[] = "D" . $i;
			}
			
			$content .= $this->_TO_function("pinTo", ["DIGITAL", "PIN", NULL, "MASK"], $pinNames);
			$content .= $this->_TO_function("pinTo", ["DIGITAL", "PIN", NULL, "PORT_MODE"], $pinNames, true);
			$content .= $this->_TO_function("pinTo", ["DIGITAL", "PIN", NULL, "PORT_IN"], $pinNames, true);
			$content .= $this->_TO_function("pinTo", ["DIGITAL", "PIN", NULL, "PORT_OUT"], $pinNames, true);
			$content .= $this->_TO_function("pinTo", ["DIGITAL", "PIN", NULL, "PORT_NUMBER"], $pinNames);
			
			$content .= $this->_TO_function("pinTo", ["ANALOG", "PIN", NULL, "MASK"], $pinNames);
			
			$content .= $this->_TO_function("pinTo", ["INTERRUPT", "PIN", NULL, "MASK"], $pinNames);
			$content .= $this->_TO_function("pinTo", ["INTERRUPT", "PIN", NULL, "GROUP_MASK"], $pinNames);
			$content .= $this->_TO_function("pinTo", ["INTERRUPT", "PIN", NULL, "GROUP"], $pinNames, true);
			$content .= $this->_TO_function("pinTo", ["INTERRUPT", "PIN", NULL, "GROUP_NUMBER"], $pinNames);
			
			
		$content .= "#endif";
		
		return $content;
	}

		private function _TO_defineConstants() {
			$content = "";
			
			$content .=  TAB() . "#define FE_NONE 255" . EOL(2);
			
			$ports = ['B', 'C', 'D', 'E', 'F'];
			for($i = 0, $size = count($ports); $i < $size; $i++) {
				$letter = $ports[$i];
				
				$content .=  $this->_getIfDefined(["DIGITAL", "PORT", $i, "MODE"], "DDR" . $letter);
				$content .=  $this->_getIfDefined(["DIGITAL", "PORT", $i, "IN"], "PIN" . $letter);
				$content .=  $this->_getIfDefined(["DIGITAL", "PORT", $i, "OUT"], "PORT" . $letter);
			}
			
			for($i = 0, $size = 3; $i < $size; $i++) {
				$content .=  $this->_getIfDefined(["INTERRUPT", "GROUP", $i], "PCMSK" . $i);
				$content .=  $this->_getIfDefined(["INTERRUPT", "GROUP", $i, "MASK"], numberToByte(1 << $i));
				$content .=  $this->_getIfDefined(["INTERRUPT", "GROUP", $i, "INTR_NAME"], "PCINT" . $i . "_vect");
			}
			
			return $content;
		}
		
		private function _TO_function($functionName, $functionParts, $elements, $isRef = false) {
			$content = "";
			
			if($isRef) {
				$returnType		= "volatile unsigned char*";
				$reference		= "&";
				$defaultReturn	= "NULL";
			} else {
				$returnType		= "unsigned char";
				$reference		= "";
				$defaultReturn	= "FE_NONE";
			}
			
			
			$content .= TAB() . $returnType . " " . $this->_TO_getFunctionName($functionName, $functionParts) . "(unsigned char element) {" . EOL();
			
				$content .= TAB(2) . "switch(element) {" . EOL();
				
					for($i = 0, $size = count($elements); $i < $size; $i++) {
						$pinConstantName = $this->_TO_getConstantName($elements[$i], $functionParts);

						$content .= TAB(3) . "#if defined(" . $pinConstantName . ")" . EOL();
							$content .= TAB(4) . "case " . $i . ":" . EOL();
								$content .= TAB(5) . "return " . $reference . $pinConstantName . ";" . EOL();
							$content .= TAB(4) . "break;" . EOL();
						$content .= TAB(3) . "#endif" . EOL();
							
					}
				$content .= TAB(4) . "default:" . EOL();
					$content .= TAB(5) . "return " . $defaultReturn . ";" . EOL();
				$content .= TAB(4) . "break;" . EOL();
							
				$content .= TAB(2) . "}" . EOL();
				
			$content .= TAB() . "}" . EOL();
			
			$content .= EOL();
			
			return $content;
		}

		private function _TO_getFunctionName($functionName, $functionParts) {
			for($i = 0, $size = count($functionParts); $i < $size; $i++) {
				if($functionParts[$i] === NULL) {
					unset($functionParts[$i]);
				}
			}
			$functionParts = array_values($functionParts);
			array_unshift($functionParts, $functionName);
			
			return $this->_getConstantName($functionParts);
		}
		
		private function _TO_getConstantName($elementName, $functionParts) {
			for($i = 0, $size = count($functionParts); $i < $size; $i++) {
				if($functionParts[$i] === NULL) {
					$functionParts[$i] = $elementName;
				}
			}
			return $this->_getConstantName($functionParts);
		}
	
	
	private function _getConstantName($constantParts) {
		$content = $this->_libName;
		foreach($constantParts as $part) {
			$content .= "_" . $part;
		}
		return $content;
	}
	
	private function _getIfDefined($constantParts, $constantName) {
		$content = "";
		
		$content .=  TAB() . "#if defined(" . $constantName . ")" . EOL();
			$content .=  TAB(2) . "#define " . $this->_getConstantName($constantParts) . " " . $constantName . EOL();
		$content .=  TAB() . "#endif" . EOL();
		
		return $content;
	}
	


}

header("Content-Type:text/plain");

$FE_compiler = new FE_compiler("compiled/");

$FE_compiler->compileFor([new ArduinoProMini(), new ArduinoNano(), new ArduinoProMicro(), new ArduinoYun(), new ArduinoTiny()]);
//$FE_compiler->compileFor(new ArduinoProMini());

//echo $FE_compiler->compileFor_PIN_TO_MASK_AND_PORT();

?>