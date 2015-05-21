<?php

require_once('PinFunctions.php');

abstract class Microcontroller {
	public $name, $AVRname;
	public $numberOfPins;
	public $pinsFunctions;
	
	public function __construct($name, $AVRname) {
		$this->name			= $name;
		$this->AVRname		= $AVRname;
		$this->numberOfPins	= count($this->pinsFunctions);
	}
	
		// convert a DigitalPort object into its AVR port
	abstract public function digitalPortToDigitalPortMode($digitalPort);
	abstract public function digitalPortToDigitalPortIn($digitalPort);
	abstract public function digitalPortToDigitalPortOut($digitalPort);
	
		// convert a DigitalPort object into its AVR pin mask
	abstract public function digitalPortToDigitalPinMask($digitalPort);
}



class Atmega328P extends Microcontroller {
	public function __construct() {
		
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
		
		parent::__construct('Atmega328P', '__AVR_ATmega328P__');
	}
	
	public function digitalPortToDigitalPortMode($digitalPort) {
		return "DDR" . $digitalPort->letter;
	}
	
	public function digitalPortToDigitalPortIn($digitalPort) {
		return "PIN" . $digitalPort->letter;
	}
	
	public function digitalPortToDigitalPortOut($digitalPort) {
		return "PORT" . $digitalPort->letter;
	}

	public function digitalPortToDigitalPinMask($digitalPort) {
		return numberToByte(1 << $digitalPort->number);
	}
	

		// TODO : abstract
		
		// reference
	public function getAnalogReferenceMask($reference) {
		switch($reference) {
			case 'DEFAULT':
				return "B01000000";
			break;
			case 'INTERNAL':
				return "B11000000";
			break;
			case 'EXTERNAL':
				return "B00000000";
			break;
			default:
				throw new Exception("Unknown analog reference " . $reference);
		}
	}
	
	public function setAnalogReference($referenceMask) {
		return "ADMUX = (ADMUX & B00111111) | " . $referenceMask . ";";
	}
	
		// prescaller
	public function getAnalogPrescalerMask($prescaler) {
		switch($prescaler) {
			case 2:
				return numberToByte(1);
			break;
			case 4:
				return numberToByte(2);
			break;
			case 8:
				return numberToByte(3);
			break;
			case 16:
				return numberToByte(4);
			break;
			case 32:
				return numberToByte(5);
			break;
			case 64:
				return numberToByte(6);
			break;
			case 128:
				return numberToByte(7);
			break;
			default:
				throw new Exception("Unknown analog prescaler " . $prescaler);
		}
	}
	
	public function setAnalogPrescaler($prescalerMask) {
		return "ADCSRA = (ADCSRA & B11111000) | " . $prescalerMask . ";";
	}

		// pin
	public function analogPortToAnalogMask($analogPort) {
		return numberToByte($analogPort->number);
	}
	
	public function setAnalogPin($pinMask) {
		return "ADMUX = (ADMUX & B11110000) | " . $pinMask . ";";
	}

		// read
	public function startAnalogConversion() {
		return "ADCSRA |= B01000000;";
	}
	
	public function getAnalogConversionStatus() {
			// true if incomplete
		return "((bool) (ADCSRA & B01000000))";
	}
	
	public function analogRead($bits) {
		switch($bits) {
			case 8:
				return "ADCH";
			break;
			case 10:
				return "(ADCL | (ADCH << 8))";
			break;
			default:
				throw new Exception("Unknown analog precision " . $reference);
		}
	}
	
}


?>