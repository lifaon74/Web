<?php

require_once('Object.class.php');

class _String extends Object {
	public $type;
	public $value;
	
	function __construct($string) {
		$this->type = 'string';
		$this->value = $string;
	}
	
	public function optimize() {
		return $this;
	}
	
	public function compile() {
		/*if(is_object($string)) {
			switch($this->value->type) {
				case 'number':
				
				break;
			}
		} else {
			return $this->value;
		}*/
	}
}
?>