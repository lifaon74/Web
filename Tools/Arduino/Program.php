<?php
/**
	Ne mène nulle part sans véritable parser
*/

header("Content-Type:text/plain");

function EOL($index = 1) {
	$str = "";
	
	for($i = 0; $i < $index; $i++) {
		$str .= "\r\n";
	}
	
	return $str;
}

function TAB($index = 1) {
	$str = "";
	
	for($i = 0; $i < $index; $i++) {
		$str .= "\t";
	}
	
	return $str;
}


function listInstructions() {
	global $commands;

	for($i = 0; $i < count($commands); $i++) {
		echo "#define PROG_" . $commands[$i] . "\t" . $i . EOL();
	}
}



$commands = [
	"NOP",
	
	"ASSIGN_UCHAR",
	"ASSIGN_UINT",
	"ASSIGN_ULONG",
	"ASSIGN_VARIABLE",
	"ASSIGN_DEREFERENCE",
	
	
	"JUMP_STATIC",
	"JUMP_DYNAMIC",
	"JUMP_CONDITIONAL",
	
	"ARDUINO_PIN_MODE",
	"ARDUINO_PIN_WRITE",
	"ARDUINO_PIN_READ",
	"ARDUINO_DELAY",
	"SERIAL_PRINT",
	
	"ADD",
	"SUB",
	"MUL",
	"DIV",
	"MOD",
	
	"INC",
	"DEC",
	
	"CMP_EQ",
	"CMP_NOT_EQ"
];

function cmd($cmd) {
	global $commands;
	$i = array_search($cmd, $commands);
	
	if($i === false) {
		return "PROG_" . "NOP";
	} else {
		return "PROG_" . $cmd;
	}
}

function arrayToString($array) {
	$string = "[";
	
	for($i = 0, $size_i = count($array); $i < $size_i; $i++) {
		if($i > 0) { $string .= ", "; }
		$string .= $array[$i];
	}
	
	$string .= "]";
	return $string;
}


class pInstruction {
	public $name, $parameters;
	
	public function __construct($name, $parameters) {
		$this->name			= $name;
		$this->parameters	= $parameters;
	}
	
	public function compile() {
		return array_merge([cmd($this->name)], $this->parameters);
	}
}

class pInstructionsSet {
	public $callback, $instructions;
	
	public function __construct($callback) {
		$this->callback		= $callback;
		$this->instructions	= [];
	}
	
	public function compile() {
		$instructions = [];
		$this->callback->__invoke($this);
		
		for($i = 0, $size_i = count($this->instructions); $i < $size_i; $i++) {
			$instruction = $this->instructions[$i];
			switch(get_class($instruction)) {
				case "pInstruction":
				case "pInstructionsSet":
					$_instructions = $instruction->compile();
					for($j = 0, $size_j = count($_instructions); $j < $size_j; $j++) {
						$instructions[] = $_instructions[$j];
					}
				break;
			}
		}
		
		return $instructions;
	}
	
	public function add($instruction) {
		$this->instructions[] = $instruction;
	}
}

class pVariable {
	public $address;
	
	public function __construct() {
		$this->address = 0;
	}
	
	public function set($value) {
		return new pInstruction("ASSIGN_ULONG", [$value, 0, 0, 0, $this->address]);
	}
}

class pLabel {
	public $address;
	
	public function __construct() {
		$this->address = 0;
	}
	
	public function jump($value) {
		return new pInstruction("ASSIGN_ULONG", [$value, 0, 0, 0, $this->address]);
	}
}



$instructions = new pInstructionsSet(function($instructionsSet) {
	$var_1 = new pVariable();
	$instructionsSet->add($var_1->set(10));
	/*$instructionsSet->add(
		new pInstructionsSet(function($instructionsSet) {
			$var_1 = new pVariable();
			$instructionsSet->add($var_1->set(11));
		})
	);*/
	
	$instructionsSet->add($var_1->set(12));
	
});

echo arrayToString($instructions->compile());

//listInstructions();

?>