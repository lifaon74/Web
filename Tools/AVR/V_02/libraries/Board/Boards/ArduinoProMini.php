<?php 

require_once(__DIR__ . '/../Board.class.php');
require_once(__DIR__ . '/../../Microcontroller/Microcontrollers/Atmega328P.php');

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