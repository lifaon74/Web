<?php
require_once('libraries/Boards.php');
require_once('libraries/Functions.php');
require_once('libraries/Digital.class.php');
require_once('libraries/Analog.class.php');


class AVR {
	public $board;
	public $microcontroller;
	
	public $LOW, $HIGH;
	
	public $digital;
	
	public function __construct($board) {
		$this->board			= $board;
		$this->frequency		= 8000000;
		
		$this->includes		= "";
		$this->digital		= new DigitalClass($this);
		$this->analog		= new AnalogClass($this);
		
	}
	
		// generate includes
	public function getIncludes() {
		$this->createInclude('constants', $this->_initConstants());
		$this->createInclude('boardSpecificsFunctions', $this->board->cppFunctions);
		return $this->includes;
	}
	
		public function createInclude($fileName, $content) {
			$this->includes .= $this->_saveAs($fileName, $content);
		}
	
	
		// save compiled files
	public function _saveAs($fileName, $content) {
		
		$fullFileName	= $fileName . ".h";
		$path			= "compiled/" . $fullFileName;
		file_put_contents($path, $content);
		return "#include \"" . $fullFileName . "\"" . EOl();
	}
	
			// initialize all constants
		private function _initConstants() {
			$content  = "";
			
				// arduino constants
			$content .= TAB() . "// arduino constants" . EOL();
			$content .= $this->defineConstant('LOW', 0);
			$content .= $this->defineConstant('HIGH', 1);
			
			$content .= $this->defineConstant('INPUT', 0);
			$content .= $this->defineConstant('OUTPUT', 1);
			$content .= $this->defineConstant('INPUT_PULLUP', 2);
			
			$content .= $this->defineConstant('LSBFIRST', 0);
			$content .= $this->defineConstant('MSBFIRST', 1);
			
			$content .= $this->defineConstant('CHANGE', 1);
			$content .= $this->defineConstant('FALLING', 2);
			$content .= $this->defineConstant('RISING', 3);
			
			$content .= $this->defineConstant('EXTERNAL', 0);
			$content .= $this->defineConstant('DEFAULT', 1);
			$content .= $this->defineConstant('INTERNAL', 3);
			
				// microcontroller constants
			$content .= EOL() . TAB() . "// microcontroller constants" . EOL();
			$content .= $this->defineConstant($this->board->microcontroller->AVRname, 1);
			$content .= $this->defineConstant('F_CPU', $this->frequency);
		
				// binary constants
			$content .= EOL() . TAB() . "// binary constants" . EOL();
			for($i = 0; $i < 256; $i++) {
				$content .= $this->defineConstant(numberToByte($i), $i, false);
			}
			
			return $content;
		}
	
		// define one constant
	public function defineConstant($name, $value = null, $protect = true) {
		$this->$name = $value;
		$content = "";
		
		if($protect) {
			$content .= "#ifndef " . $name . EOL() . TAB();
		}
		
			$content .= "#define " . $name;
		
		if($value !== null) {
			$content .=  " " . $value;
		}
		
		$content .= EOL();

		if($protect) {
			$content .= "#endif" . EOL();
		}
		
		return $content;
	}

			// check if input is a constant
		public function formatConstant($const) {
			if(is_string($const) && isset($this->$const)) {
				return $this->$const;
			} else {
				return $const;
			}
		}
		
	
	
		/*** START - old tests ***/	
		
			// add a function
		public function addFunction($function) {
			$this->functions .= $function;
		}
	
			// create a function
		public function createFunction($functionName, $returnType, $arguments, $content) {
			$this->functions[$functionName] = (object) [
				"returnType"	=> $returnType,
				"arguments"		=> $arguments,
				"content"		=> $content
			];
		}
		
			// call a function
		public function callFunction($functionName, $arguments) {
			return $functionName . "(" . $this->_compileFunctionArguments($arguments, false) . ");";
		}
		
			private function _compileFunctions($functions) {
				$content = "";
				
				foreach($functions as $functionName => $function) {
					$content .= $function->returnType . " " . $functionName . "(" .  $this->_compileFunctionArguments($function->arguments) . ") {" . EOL();
					$content .= TAB(1, $function->content) . EOL();
					$content .= "}" . EOL();
				}
				
				return $content;
			}
			
			private function _compileFunctionArguments($arguments, $addType = true) {
				$args	= "";
				$i		= 0;
				foreach($arguments as $name => $type) {
					if($i > 0) { $args .= ", "; }
					if($addType) { $args .= $type . " "; }
					$args .= $name;
					$i++;
				}
				return $args;
			}
			
		/*** END - old tests ***/
		
		
		// generate an if condition
	public function _if($bool, $true, $false = "") {
		$bool = $this->formatConstant($bool);
		
		if(is_numeric($bool) || is_bool($bool)) {
			if($bool) {
				return $true;
			} else {
				return $false;
			}
		} else {
			$content  = "";
				$content  .= 	"if(" . $bool . ") {" . EOL() .
									TAB() . $true .  EOL() .
								"}";
			if($false != "") {	
				$content  .= 	" else { "  .  EOL() .
									TAB() . $false .  EOL() .
								"}";
			}
			return $content;
		}
	}
	
		// add ;\r\n
	public function cmd($command) {
		return $command . ";" . EOL(1);
	}

}

$t1 = microtime();

$avr = new AVR(new ArduinoProMini());

if(isset($_REQUEST['path'])) {
	header("Content-Type:text/plain");
	
	if(file_exists($_REQUEST['path'])) {
		ob_start();
		include($_REQUEST['path']);
		$content = ob_get_contents();
		$avr->_saveAs('main', $content);
		ob_end_clean();
		
		$t2 = microtime();
		echo $content . EOL(10);
		
		echo "compiled in : " . floor(($t2 - $t1) * 1000) . "ms" . EOL();
		echo "size of main.h : " . strlen($content) . EOL();
	} else {
		echo "File not found";
	}
}
?>
