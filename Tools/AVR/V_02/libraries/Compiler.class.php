<?php 

require_once(__DIR__ . '/Functions.php');
require_once(__DIR__ . '/Precompiler.class.php');
require_once(__DIR__ . '/Board/Boards/ArduinoProMini.php');
require_once(__DIR__ . '/Microcontroller/Microcontrollers/Atmega328P.php');

$precompiler = null;


class Compiler {
	public $tempPath;
	public $verbose;
	
	public function __construct() {
		$this->tempPath		= "compiled/";
		$this->verbose		= true;
		$this->precompiler	= new Precompiler();
		
		global $precompiler;
		$precompiler = $this->precompiler;
	}
	
	
	public function compile($filePath, $microcontroller) {
		header("Content-Type:text/plain");
		
		if(file_exists($filePath)) {
			$pathinfo = pathinfo($filePath);
			
			$this->_echo("Compiling \"" . $pathinfo['basename'] . "\"");
			
			$precompiledFilePath = $this->precompiler->_include($filePath, 0);
			
			echo EOL(2);
			echo file_get_contents($precompiledFilePath);
			echo EOL(2);
			
			$precompiledPathinfo		= pathinfo($precompiledFilePath);
			$compiledFilePathWithoutExtension	= $this->tempPath . $precompiledPathinfo['filename'];
			$this->_exec_cmd("avr-gcc " . $precompiledFilePath . " -o " . $compiledFilePathWithoutExtension . ".o -c -g -Os -mmcu=" . $microcontroller->AVRDudeName, true);
			$this->_exec_cmd("avr-gcc " . $compiledFilePathWithoutExtension . ".o -o " . $compiledFilePathWithoutExtension . ".elf -g -mmcu=" . $microcontroller->AVRDudeName, true);
			$this->_exec_cmd("avr-objcopy -j .text -j .data -O ihex " . $compiledFilePathWithoutExtension . ".elf " . $compiledFilePathWithoutExtension . ".hex", true);
			
			$pathOfFinalCompiledFile = $compiledFilePathWithoutExtension . ".hex";
			$this->_echo("Final program in \"" . $pathOfFinalCompiledFile . "\"");
			$this->_echo("Upload it with the command \"avrdude -p " . $microcontroller->AVRDudeName . " -c arduino -P COM14 -b 57600 -e -D -U flash:w:" . $pathOfFinalCompiledFile . ":i\"");
			
		} else {
			throw new Exception("File  " . $filePath . " doesn't exist.");
		}
	}
	
	
	private function _echo($message) {
		if($this->verbose) {
			echo $message . EOL();
		}
	}
	
	private function _exec_cmd($cmd, $createBatch = false) {
		$this->_echo("CMD : \"" . $cmd . "\" ... ");
		exec($cmd, $output, $failed);
		if($failed) {
			$this->_echo("ERROR");
			if($createBatch) {
				file_put_contents("last_command.bat", $cmd . EOL() . "pause");
			}
			exit();
		} else {
			$this->_echo("OK");
		}
	}
	
}


$compiler = new Compiler();

?>