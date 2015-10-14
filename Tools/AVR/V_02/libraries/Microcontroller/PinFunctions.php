<?php

abstract class PinFunction {
	public $type;
	
	public function __construct($type) {
		$this->type = $type;
	}
}


class VCC extends PinFunction {
	public function __construct() {
		parent::__construct('VCC');
	}
}

class GND extends PinFunction {
	public function __construct() {
		parent::__construct('GND');
	}
}


class DigitalPort extends PinFunction {
	public $letter, $number;

	public function __construct($letter, $number) {
		$this->letter	= $letter;
		$this->number	= $number;
		parent::__construct('digitalPort');
	}
}

class AnalogPort extends PinFunction {
	public $number;
	
	public function __construct( $number) {
		$this->number	= $number;
		parent::__construct('analogPort');
	}
}

class InterruptPort extends PinFunction {
	public $number;

	public function __construct($number) {
		$this->number	= $number;
		parent::__construct('interruptPort');
	}
}

?>