<?php
trait Digital {
	
	protected function digital_init() {
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
			return $this->constructor->_shiftLeft(1, $digitalPort->number);
		}, $this->_pinIndexes, false);
	}
	
		// pin to ...
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

}
?>