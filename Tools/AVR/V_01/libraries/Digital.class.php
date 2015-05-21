<?php 

class DigitalClass {
	protected $constructor;
	
	public function __construct($constructor) {
		$this->constructor = $constructor;
	}
	
	public function mode($pin, $mode) {
		$pin = $this->constructor->formatConstant($pin);
		$mode = $this->constructor->formatConstant($mode);
		
		switch($mode) {
			case $this->constructor->INPUT_PULLUP:
				return
					$this->modeInput($pin) . ";" . EOL() .
					$this->write($pin, $this->constructor->HIGH) . ";";
			break;
			default:
				return $this->constructor->_if($mode, $this->modeOutput($pin), $this->modeInput($pin));
		}
	}
	
		public function modeInput($pin) {
			$pin = $this->constructor->formatConstant($pin);
			return $this->constructor->board->callFunction('pinToDigitalPortMode', $pin) . " &= " . invertByte($this->constructor->board->callFunction('pinToDigitalPinMask', $pin)) . ";";
		}
		
		public function modeOutput($pin) {
			$pin = $this->constructor->formatConstant($pin);
			return $this->constructor->board->callFunction('pinToDigitalPortMode', $pin) . " |= " . $this->constructor->board->callFunction('pinToDigitalPinMask', $pin) . ";";
		}
	
	
	public function read($pin) {
		$pin = $this->constructor->formatConstant($pin);
		return "((bool) (" . $this->constructor->board->callFunction('pinToDigitalPortIn', $pin) . " & " . $this->constructor->board->callFunction('pinToDigitalPinMask', $pin) . "))";
	}
	
	
	public function write($pin, $state) {
		$pin = $this->constructor->formatConstant($pin);
		$state = $this->constructor->formatConstant($state);
		return $this->constructor->_if($state, $this->writeHigh($pin), $this->writeLow($pin));
	}
	
		public function writeLow($pin) {
			$pin = $this->constructor->formatConstant($pin);
			return $this->constructor->board->callFunction('pinToDigitalPortOut', $pin) . " &= " . invertByte($this->constructor->board->callFunction('pinToDigitalPinMask', $pin)) . ";";
		}
		
		public function writeHigh($pin) {
			$pin = $this->constructor->formatConstant($pin);
			return $this->constructor->board->callFunction('pinToDigitalPortOut', $pin) . " |= " . $this->constructor->board->callFunction('pinToDigitalPinMask', $pin) . ";";
		}
		
}

?>