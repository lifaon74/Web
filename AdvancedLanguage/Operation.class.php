<?php

require_once('Object.class.php');

class _Operation extends Object {
	public $type;
	public $data_1;
	public $operation;
	public $data_2;
	
	function __construct($data_1, $operation, $data_2) {
		$this->type = 'operation';
		
		if(!is_object($data_1)) { trigger_error("first argument of Operation must be an Object", E_USER_ERROR); }
		$this->data_1 = $data_1;
		
		$this->operation = $operation;
		
		if(!is_object($data_2)) { trigger_error("last argument of Operation must be an Object", E_USER_ERROR); }
		$this->data_2 = $data_2;
	}
	
	public function optimize() {
		$data_1_optimized = $this->data_1->optimize();
		$data_2_optimized = $this->data_2->optimize();
		
		switch($data_1_optimized->type) {
			case 'number':
				switch($data_2_optimized->type) {
					case 'number':
						switch($this->operation) {
							case '+':
								return new _Number($data_1_optimized->value + $data_2_optimized->value);
							break;
							case '-':
								return new _Number($data_1_optimized->value - $data_2_optimized->value);
							break;
							case '/':
								return new _Number($data_1_optimized->value / $data_2_optimized->value);
							break;
							case '*':
								return new _Number($data_1_optimized->value * $data_2_optimized->value);
							break;
						}
					break;
				}
			break;
		}
		
		return new _Operation($data_1_optimized, $this->operation, $data_2_optimized);
	}
	
	public function compile() {
		
	}
}
?>