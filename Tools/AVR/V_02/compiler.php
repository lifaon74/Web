<?php
require_once('libraries/Functions.php');
require_once('libraries/Board/Boards/ArduinoProMini.php');
require_once('libraries/Microcontroller/Microcontrollers/Atmega328P.php');

class Compiler {
	public $verbose;
	public function __construct() {
		$this->verbose = true;
	}
	
	
	public function compile($filePath) {
		header("Content-Type:text/plain");
		
		if(file_exists($filePath)) {
			$pathinfo = pathinfo($filePath);
			
			
			$this->_echo("Compiling \"" . $pathinfo['basename'] . "\"");
			//$this->_echo($optimizer->_include($_REQUEST['path'], 0));
			
			/*$this->_exec_cmd("avr-gcc compiled/main.c -o compiled/main.o -c -g -Os -mmcu=" . $microcontroller->AVRDudeName);
			$this->_exec_cmd("avr-gcc compiled/main.o -o compiled/main.elf -g -mmcu=" . $microcontroller->AVRDudeName);
			$this->_exec_cmd("avr-objcopy -j .text -j .data -O ihex compiled/main.elf " . $compiledFileName . ".hex");*/
			
		} else {
			throw new Exception("File  " . $filePath . " doesn't exist.");
		}
	}
	
	private function _echo($message) {
		if($this->verbose) {
			echo $message;
		}
	}
	
	private function _exec_cmd($cmd) {
		echo "CMD : \"" . $cmd . "\" ... ";
		exec($cmd, $output, $failed);
		if($failed) {
			echo "ERROR" . EOL();
			exit();
		} else {
			echo "OK" . EOL();
		}
	}
	
}






function timed_function($functionName, $callback) {
	echo "Start " . $functionName . "..." . EOL();
	$t1 = microtime();
	$callback();
	$t2 = microtime();
	echo "DONE in : " . floor(($t2 - $t1) * 1000) . "ms" . EOL();
}








if(isset($_REQUEST['path'])) {
	if(file_exists($_REQUEST['path'])) {
		
		$compiledFileName = basename($_REQUEST['path'], '.php');

		echo "Compiling \"" . $compiledFileName . "\"" . EOL(2);
		
		
			timed_function("precompilation", function() use($optimizer, $ArduinoProMini, $microcontroller){
				
				echo $optimizer->_include($_REQUEST['path'], 0);
				
				/*ob_start();
					include($_REQUEST['path']);
					$code = ob_get_contents();
				ob_end_clean();
				
				$code = $optimizer->getIncludes() . EOL(2) . $code;
				$optimizer->saveAs('main.c', $code);
				
				echo $code . EOL(2);*/
			});
		
		echo EOl();
		
		timed_function("compilation", function() use($optimizer, $ArduinoProMini, $microcontroller, $compiledFileName) {
			exec_cmd("avr-gcc compiled/main.c -o compiled/main.o -c -g -Os -mmcu=" . $microcontroller->AVRDudeName);
			exec_cmd("avr-gcc compiled/main.o -o compiled/main.elf -g -mmcu=" . $microcontroller->AVRDudeName);
			exec_cmd("avr-objcopy -j .text -j .data -O ihex compiled/main.elf " . $compiledFileName . ".hex");
		});
		
		echo EOl();
		
		$pathOfFinalCompiledFile = $compiledFileName . ".hex";
		echo "Final program in \"" . $pathOfFinalCompiledFile . "\"" . EOL();
		echo "Upload it with the command \"avrdude -p " . $microcontroller->AVRDudeName . " -c arduino -P COM14 -b 57600 -e -D -U flash:w:" . $pathOfFinalCompiledFile . ":i\"";
		
	} else {
		echo "File not found";
	}
}
?>
