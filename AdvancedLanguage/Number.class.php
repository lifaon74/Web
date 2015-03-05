<?php

require_once('Object.class.php');

class _Number extends Object {
	public $type;
	public $value;
	
	function __construct($number) {
		$this->type = 'number';
		$this->value = $number;
	}
	
	public function optimize() {
		return $this;
	}
	
	public function compile() {
	
	}
}
?>