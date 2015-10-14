<?php 
trait Interrupt {
	
		/** 
			Interrupt
		**/	
		
	protected function interrupt_init() {
		
		$this->_autoCreateTryCatchFunction("interrupt_pinToGroupNumber", function($pin) {
			$interruptPort = $this->_pinToPinFunction($pin, 'interruptPort');
			return $this->interrupt_interruptNumberToGroupNumber($interruptPort->number);
		}, $this->_pinIndexes, false);
		
		$this->_autoCreateTryCatchFunction("interrupt_pinToGroupMask", function($pin) {
			return $this->interrupt_groupNumberToGroupMask($this->interrupt_pinToGroupNumber($pin));
		}, $this->_pinIndexes, false);
		
		$this->_autoCreateTryCatchFunction("interrupt_pinToPinMask", function($pin) {
			$interruptPort = $this->_pinToPinFunction($pin, 'interruptPort');
			return $this->constructor->_shiftLeft(1, $this->interrupt_interruptNumberToBitshiftLeftIndex($interruptPort->number));
		}, $this->_pinIndexes, false);
		
		$this->_autoCreateTryCatchFunction("interrupt_pinToPinChangeMaskRegister", function($pin) {
			$interruptPort = $this->_pinToPinFunction($pin, 'interruptPort');
			return "PCMSK" . $this->interrupt_interruptNumberToGroupNumber($interruptPort->number);
		}, $this->_pinIndexes, false);
		
		$this->_autoCreateTryCatchFunction("interrupt_groupNumberToGroupMask", function($groupNumber) {
			return $this->constructor->_shiftLeft(1, $groupNumber);
		}, [0, 1, 2], false);
		
	}
	
	
	public function interrupt_interruptNumberToGroupNumberAndPinBitshiftLeftIndex($interruptNumber) {
		foreach($this->interruptGroupNumberToInterruptNumber as $groupNumber => $interruptNumbers) {
			for($i = 0, $size_i = count($interruptNumbers); $i < $size_i; $i++) {
				if($interruptNumbers[$i] == $interruptNumber) {
					return [$groupNumber, $i];
				}
			}
		}
		
		throw new Exception("interruptNumber  " . $interruptNumber . " doesn't exist.");
	}
	
	public function interrupt_interruptNumberToGroupNumber($interruptNumber) {
		list($groupNumber, $bitshiftLeftIndex) = $this->interrupt_interruptNumberToGroupNumberAndPinBitshiftLeftIndex($interruptNumber);
		return $groupNumber;
	}
	
	public function interrupt_interruptNumberToBitshiftLeftIndex($interruptNumber) {
		list($groupNumber, $bitshiftLeftIndex) = $this->interrupt_interruptNumberToGroupNumberAndPinBitshiftLeftIndex($interruptNumber);
		return $bitshiftLeftIndex;
	}
	
	
	
	
		// pin to ...
	
	public function interrupt_pinToGroupNumber($pin) {
		return $this->_callTryCatchFunction("interrupt_pinToGroupNumber", $pin);
	}
	
	public function interrupt_pinToGroupMask($pin) {
		return $this->_callTryCatchFunction("interrupt_pinToGroupMask", $pin);
	}
	
	public function interrupt_pinToPinMask($pin) {
		return $this->_callTryCatchFunction("interrupt_pinToPinMask", $pin);
	}
	
	public function interrupt_pinToPinChangeMaskRegister($pin) {
		return $this->_callTryCatchFunction("interrupt_pinToPinChangeMaskRegister", $pin);
	}
	
	public function interrupt_groupNumberToGroupMask($groupNumber) {
		return $this->_callTryCatchFunction("interrupt_groupNumberToGroupMask", $groupNumber);
	}
	
	
		// interrupt_enable
	public function interrupt_enable() {
		return "SREG |= " . byteToNumber("B10000000") . ";" . EOL();
	}
	
		// interrupt_disable
	public function interrupt_disable() {
		return "SREG &= " . byteToNumber("B01111111") . ";" . EOL();
	}
	
			// interrupt_state
		public function interrupt_state($state) {
			return $this->constructor->_if($state,
				function() { return $this->interrupt_enable(); },
				function() { return $this->interrupt_disable(); }
			);
		}
	

		// interrupt_enableOnGroup
	public function interrupt_enableOnGroup($groupNumber) {
		return "PCICR |= " . $this->interrupt_groupNumberToGroupMask($groupNumber) . ";" . EOL();
	}
	
		// interrupt_disableOnGroup
	public function interrupt_disableOnGroup($groupNumber) {
		return "PCICR &= " . $this->constructor->_invert($this->interrupt_groupNumberToGroupMask($groupNumber)) . ";" . EOL();
	}
	
			// interrupt_groupState
		public function interrupt_groupState($groupNumber, $state) {
			return $this->constructor->_if($state,
				function() use($groupNumber) { return $this->interrupt_enableOnGroup($groupNumber); },
				function() use($groupNumber) { return $this->interrupt_disableOnGroup($groupNumber); }
			);
		}
	
	
		// interrupt_enableOnPin
	public function interrupt_enableOnPin($pin) {
		return $this->interrupt_pinToPinChangeMaskRegister($pin) . " |= " . $this->interrupt_pinToPinMask($pin) . ";" . EOL();
	}
	
		// interrupt_disableOnPin
	public function interrupt_disableOnPin($pin) {
		return $this->interrupt_pinToPinChangeMaskRegister($pin) . " &= " . $this->constructor->_invert($this->interrupt_pinToPinMask($pin)) . ";" . EOL();
	}
	
			// interrupt_pinState
		public function interrupt_pinState($pin, $state) {
			return $this->constructor->_if($state,
				function() use($pin) { return $this->interrupt_enableOnPin($pin); },
				function() use($pin) { return $this->interrupt_disableOnPin($pin); }
			);
		}
	
}
?>