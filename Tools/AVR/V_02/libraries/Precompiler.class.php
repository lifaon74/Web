<?php 

class Precompiler {
	public $tempPath;
	public $code;
	
	public function __construct() {
		$this->tempPath		= "precompiled/";
		
		$this->constantsH = fopen($this->tempPath . "constants.h", "w+");
		$this->functionsH = fopen($this->tempPath . "functions.h", "w+");
		
			// REMOVE
		$this->code = (object) [
			"constants"	=> "",
			"functions"	=> "",
			"includes"	=> ""
		];
		
		$this->_initConstants();
	}
	
	public function __destruct() {
		fclose($this->constantsH);
		fclose($this->functionsH);
	}
	
	
	/***
		INIT
	***/
	
			// initialize all constants
		private function _initConstants() {
			$this->inConstantsH("typedef volatile unsigned char RegisterPointer;" . EOL());
			
			
			$this->inConstantsH($this->_define('LOW', 0));
			$this->inConstantsH($this->_define('HIGH', 1));
			
			$this->inConstantsH($this->_define('INPUT', 0));
			$this->inConstantsH($this->_define('OUTPUT', 1));
			$this->inConstantsH($this->_define('INPUT_PULLUP', 2));
			
			$this->inConstantsH($this->_define('LSBFIRST', 0));
			$this->inConstantsH($this->_define('MSBFIRST', 1));
			
			$this->inConstantsH($this->_define('NONE', 0));
			$this->inConstantsH($this->_define('CHANGE', 1));
			$this->inConstantsH($this->_define('FALLING', 2));
			$this->inConstantsH($this->_define('RISING', 3));
			
			$this->inConstantsH($this->_define('EXTERNAL', 0));
			$this->inConstantsH($this->_define('DEFAULT', 1));
			$this->inConstantsH($this->_define('INTERNAL', 3));
		
			$this->inConstantsH($this->_define('DISABLE', 0));
			$this->inConstantsH($this->_define('ENABLE', 1));
			
			for($i = 0; $i < 256; $i++) {
				$this->inConstantsH($this->_define(numberToByte($i), $i, false));
			}
		}
		
	
	public function inConstantsH($code) {
		fwrite($this->constantsH, $code);
	}
	
	public function inFunctionsH($code) {
		fwrite($this->functionsH, $code);
	}
	
	/*public function getIncludes() {
		return
			$this->_include($this->tempPath . "constants.h") .
			$this->_include($this->tempPath . "functions.h")
		;
	}*/
	
	
	/***
		Functions
	***/
	
	
	public function _include($filePath, $returnType = 1) { // 0 => raw, 1 => absolute, 2 => relative
		
		$pathinfo = pathinfo($filePath);

		switch($pathinfo['extension']) {
			case 'php':
				if(file_exists($filePath) || $returnType == 2) {
					ob_start();
						include($filePath);
						$code = ob_get_contents();
					ob_end_clean();
					
					$includeFilePath = $this->tempPath . $pathinfo['filename'];
					file_put_contents($includeFilePath, $code);
				} else {
					throw new Exception("File  " . $filePath . " doesn't exist.");
				}
			break;
			case 'h':
			case 'c':
			case 'cpp':
				$includeFilePath = $filePath;
			break;
			default:
				throw new Exception("File  " . $filePath . " has not a valid extension.");
		}
		
		
		switch($returnType) {
			case 0:
				return $includeFilePath;
			break;
			case 1:
				return "#include \"" . $includeFilePath . "\"" . EOL();
			break;
			case 2:
				return "#include <" . $includeFilePath . ">" . EOL();
			break;
			default:
				throw new Exception("Unknown $returnType : " . $returnType);
		}
	}
	
	public function _define($name, $value = null, $protect = true) {
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
		
		return $code;
	}
	
	
	
	
		// define a function
	public function defineFunction($code) {
		$this->code->functions .= $code;
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
	
	
	public function _shiftLeft($byte, $shift) {
		$byte = $this->reduceExpression($byte);
		$shift = $this->reduceExpression($shift);
		if(is_numeric($byte) && is_numeric($shift)) {
			return $byte << $shift;
		} else {
			return "(" . $byte . " << " . $shift . ")";
		}
	}
	
	public function _shiftRight($byte, $shift) {
		$byte = $this->reduceExpression($byte);
		$shift = $this->reduceExpression($shift);
		if(is_numeric($byte) && is_numeric($shift)) {
			return $byte >> $shift;
		} else {
			return "(" . $byte . " >> " . $shift . ")";
		}
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


?>