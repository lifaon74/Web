<?php

require_once(__DIR__ . '/../Microcontroller.class.php');

class Atmega328P extends Microcontroller {
	use Digital, Analog, Interrupt;
	
	public function __construct($constructor, $frequency) {
		
		$this->pinsFunctions = [
			[new DigitalPort('D', 3), new InterruptPort(19)], // 1
			[new DigitalPort('D', 4), new InterruptPort(20)], // 2
			[new GND()], // 3
			[new VCC()], // 4
			[new GND()], // 5
			[new VCC()], // 6
			[new DigitalPort('B', 6), new InterruptPort(6)], // 7
			[new DigitalPort('B', 7), new InterruptPort(7)], // 8
			[new DigitalPort('D', 5), new InterruptPort(21)], // 9
			[new DigitalPort('D', 6), new InterruptPort(22)], // 10
			
			[new DigitalPort('D', 7), new InterruptPort(23)], // 11
			[new DigitalPort('B', 0), new InterruptPort(0)], // 12
			[new DigitalPort('B', 1), new InterruptPort(1)], // 13
			[new DigitalPort('B', 2), new InterruptPort(2)], // 14
			[new DigitalPort('B', 3), new InterruptPort(3)], // 15
			[new DigitalPort('B', 4), new InterruptPort(4)], // 16
			[new DigitalPort('B', 5), new InterruptPort(5)], // 17
			[/*AVCC*/], // 18
			[ new AnalogPort(6)], // 19
			[/*AREF*/], // 20
			
			[new GND()], // 21
			[new AnalogPort(7)], // 22
			[new DigitalPort('C', 0), new AnalogPort(0), new InterruptPort(8)], // 23
			[new DigitalPort('C', 1), new AnalogPort(1), new InterruptPort(9)], // 24
			[new DigitalPort('C', 2), new AnalogPort(2), new InterruptPort(10)], // 25
			[new DigitalPort('C', 3), new AnalogPort(3), new InterruptPort(11)], // 26
			[new DigitalPort('C', 4), new AnalogPort(4), new InterruptPort(12)], // 27
			[new DigitalPort('C', 5), new AnalogPort(5), new InterruptPort(13)], // 28
			[new DigitalPort('C', 6), new InterruptPort(14)], // 29
			[new DigitalPort('D', 0), new InterruptPort(16)], // 30
			
			[new DigitalPort('D', 1), new InterruptPort(17)], // 31
			[new DigitalPort('D', 2), new InterruptPort(18)]  // 32
		];
		
		$this->interruptGroupNumberToInterruptNumber = [
			"0"	=> [ 0,  1,  2,  3,  4,  5,  6,  7],
			"1"	=> [ 8,  9, 10, 11, 12, 13, 14],
			"2"	=> [16, 17, 18, 19, 20, 21, 22, 23]
		];
		
		parent::__construct('Atmega328P', '__AVR_ATmega328P__', 'atmega328p', $constructor, $frequency);
		
		$this->_initFunctions();
	}
	
		private function _initFunctions() {	
			$this->digital_init();
			$this->analog_init();
			$this->interrupt_init();
		}
		
}

?>