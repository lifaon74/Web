<?php
require_once('libraries/main/Functions.php');
require_once('libraries/main/Boards.php');
require_once('libraries/main/Microcontrollers.php');


class Optimizer {
	public $code;
	
	public function __construct() {
		$this->code = (object) [
			"constants"	=> "",
			"functions"	=> "",
			"includes"	=> ""
		];
		
		$this->_initConstants();
	}
	
	/***
		INIT
	***/
	
			// initialize all constants
		private function _initConstants() {
			
				// arduino constants
			$this->defineConstant('LOW', 0);
			$this->defineConstant('HIGH', 1);
			
			$this->defineConstant('INPUT', 0);
			$this->defineConstant('OUTPUT', 1);
			$this->defineConstant('INPUT_PULLUP', 2);
			
			$this->defineConstant('LSBFIRST', 0);
			$this->defineConstant('MSBFIRST', 1);
			
			$this->defineConstant('CHANGE', 1);
			$this->defineConstant('FALLING', 2);
			$this->defineConstant('RISING', 3);
			
			$this->defineConstant('EXTERNAL', 0);
			$this->defineConstant('DEFAULT', 1);
			$this->defineConstant('INTERNAL', 3);
		
				// binary constants
			for($i = 0; $i < 256; $i++) {
				 $this->defineConstant(numberToByte($i), $i, false);
			}
		}
		
	
	/***
		Functions
	***/
	
		// save code
	public function saveAs($fileName, $code) {
		$path			= "compiled/" . $fileName;
		file_put_contents($path, $code);
	}
	
	
		// define a constant
	public function defineConstant($name, $value = null, $protect = true) {
		$this->$name = $value;
		define($name, $value);
		$code = "";
		
		if($protect) {
			$code .= "#ifndef " . $name . EOL() . TAB();
		}
		
			$code .= "#define " . $name;
		
		if($value !== null) {
			$code .=  " " . $value;
		}
		
		$code .= EOL();

		if($protect) {
			$code .= "#endif" . EOL();
		}
		
		$this->code->constants .= $code;
	}
	
		// define a function
	public function defineFunction($code) {
		$this->code->functions .= $code;
	}
	
		// define an include
	public function defineInclude($fileName, $local = false, $code = null) {
		
		if($code !== null) {
			$this->saveAs($fileName, $code);
		}
		
		$this->code->includes .= "#include " . ($local ? "\"" : "<") . $fileName . ($local ? "\"" : ">") . EOl();
	}
	
	public function getIncludes() {
		$this->defineInclude('stdlib.h', false);
		$this->defineInclude('stdbool.h', false);
		$this->defineInclude('string.h', false);
		$this->defineInclude('math.h', false);
		
		$this->defineInclude('avr/pgmspace.h', false);
		$this->defineInclude('avr/io.h', false);
		$this->defineInclude('avr/interrupt.h', false);

		$this->defineInclude('constants.h', true, $this->code->constants);
		$this->defineInclude('functions.h', true, $this->code->functions);
		return $this->code->includes;
	}
	
		// generate a cpp function which convert a number into something else
	public function _AToB_function($functionName, $AToB, $isRef = false) {
		if($isRef) {
			$returnType		= "volatile unsigned char*";
			$reference		= "&";
			$defaultReturn	= "NULL";
		} else {
			$returnType		= "unsigned char";
			$reference		= "";
			$defaultReturn	= "element";
		}
				
		$code  = "";
		
		$code .= TAB(0) . $returnType . " " . $functionName . "(unsigned char element) {" . EOL();
			$code .= TAB(1) . "switch(element) {" . EOL();
				foreach($AToB as $A => $B) {
					//$code .= TAB(2) . "#if defined(" . $B . ")" . EOL();
						$code .= TAB(2) . "case " . $A . ":" . EOL();
							$code .= TAB(3) . "return " . $reference . $B . ";" . EOL();
						$code .= TAB(2) . "break;" . EOL();
					//$code .= TAB(2) . "#endif" . EOL();	
				}
			$code .= TAB(2) . "default:" . EOL();
				$code .= TAB(3) . "return " . $defaultReturn . ";" . EOL();
			$code .= TAB(2) . "break;" . EOL();
						
			$code .= TAB(1) . "}" . EOL();
			
		$code .= TAB(0) . "}" . EOL();
		
		$code .= EOL();
			
		return $code;
	}
			
	
	public function reduceExpression($expression) {
		if(is_string($expression) && isset($this->$expression)) {
			$expression = $this->$expression;
		}
		
		if(is_numeric($expression)) {
			$expression = floatval($expression);
		}
		
		return $expression;
	}
	
	
	public function _invert($byte) {
		$byte = $this->reduceExpression($byte);
		if(is_numeric($byte)) {
			return (~$byte) & 0xFF;
		} else {
			return "~" . $byte;
		}
	}
	
		// generate an if condition
	public function _if($bool, $true, $false = null) {
		$bool = $this->reduceExpression($bool);
		
		if(is_numeric($bool) || is_bool($bool)) {
			if($bool) {
				return $true();
			} else {
				return $false();
			}
		} else {
			$code  = "";
				$code  .= 	"if(" . $bool . ") {" . EOL() .
								TAB() . $true() .
							"}";
			if($false !== null) {
				$false = $false();
				if($false != "") {
					$code  .= 	" else { "  .  EOL() .
									TAB() . $false .
								"}";
				}
			}
			return $code;
		}
	}

}

$t1 = microtime();

$optimizer = new Optimizer();
$ArduinoProMini = new ArduinoProMini($optimizer, 8000000);
$microcontroller = $ArduinoProMini->microcontroller;


header("Content-Type:text/plain");



if(isset($_REQUEST['path'])) {
	if(file_exists($_REQUEST['path'])) {
		ob_start();
			include($_REQUEST['path']);
			$code = ob_get_contents();
		ob_end_clean();
		
		$code = $optimizer->getIncludes() . EOL(2) . $code;
		$optimizer->saveAs('main.c', $code);
		
		$t2 = microtime();
		
		echo $code . EOL(10);
		
		echo "compiled in : " . floor(($t2 - $t1) * 1000) . "ms" . EOL();
		echo "size of main.c : " . strlen($code) . EOL();
	} else {
		echo "File not found";
	}
}
?>
