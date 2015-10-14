<?php

require_once(__DIR__ . '/PinFunctions.php');
require_once(__DIR__ . '/../AlternativeFunctions.class.php');

require_once(__DIR__ . '/Digital.class.php');
require_once(__DIR__ . '/Analog.class.php');
require_once(__DIR__ . '/Interrupt.class.php');

abstract class Microcontroller extends AlternativeFunctions {
	
	public		$name, $AVRname, $AVRDudeName;
	protected	$constructor;
	public		$frequency;
	public		$pinsFunctions, $numberOfPins;

	
	public function __construct($name, $AVRname, $AVRDudeName, $constructor, $frequency) {
		$this->name			= $name;
		$this->AVRname		= $AVRname;
		$this->AVRDudeName	= $AVRDudeName;
		$this->constructor	= $constructor;
		$this->frequency	= $frequency;
		
		$this->numberOfPins	= count($this->pinsFunctions);
		
		$this->constructor->inConstantsH($this->constructor->_define($this->AVRname));
		$this->constructor->inConstantsH($this->constructor->_define('F_CPU', $this->frequency));
		
		$this->_functions	= [];
		$this->_pinIndexes	= [];
		
		for($i = 0; $i < $this->numberOfPins; $i++) {
			$this->_pinIndexes[$i] = $i;
		}
		
	}
	
		// convert a pin into a pinFunction object
	protected function _pinToPinFunction($pin, $pinFunctionsType) {
		if(isset($this->pinsFunctions[$pin])) {
			$pinFunctions = $this->pinsFunctions[$pin];
		} else {
			throw new Exception("Pin " . $pin . " doesn't exist.");
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


?>