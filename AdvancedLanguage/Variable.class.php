<?php

class _Variable extends Object {
	public $type;
	public $data;
	
	function __construct($data = null) {
		$this->type = 'variable';
		
		if($data === null) {
			$this->data = new _Null();
		} else {
			$this->data = $data;
		}
	}
	
	public function set($data) {
		$this->data = $data;
	}
	
	public function get() {
		return $this->data;
	}
	
	public function optimize() {
		return $this->data;
	}
	
	public function compile() {
		
	}
}

class _Null extends Object {
	public $type;
	
	function __construct($data = null) {
		$this->type = 'null';
	}
	
	public function optimize() {
		return $this;
	}
	
	public function compile() {
		
	}
}
?>