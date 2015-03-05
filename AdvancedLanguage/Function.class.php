<?php

class _Function extends Object {
	public $type;
	public $instructions;
	public $arguments;
	
	function __construct($instructions) {
		$this->type = 'function';
		$this->instructions = new _Null();
	}
	
	public function setInstructions($instructions) {
		$this->instructions = $instructions;
	}
	
	public function call($arguments) {
		$this->arguments = $arguments;
	}
	
	public function getArgument($argumentNumber) {
		return $this->arguments[$argumentNumber];
	}
	
	public function optimize() {
		
	}
	
	public function compile() {
		
	}
}
?>