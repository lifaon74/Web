<?php

class ComputerController {
	
	function __construct() {
		switch(PHP_OS) {
			case 'WIN32':
			case 'WINNT':
				$this->OS = 'Windows';
			break;
			case 'Linux':
				$this->OS = 'Linux';
			break;
			break;
			case 'Darwin':
				$this->OS = 'MacOS';
			break;
			case 'FreeBSD':
				$this->OS = 'FreeBSD';
			break;
			default:
				$this->OS = NULL;
		}
	}
	
	public function shutdown() {
		switch($this->OS) {
			case 'Windows':
				passthru('shutdown /s');
			break;
			case 'Linux':
				passthru('/sbin/halt');
			break;
		}
	}
	
	public function reboot() {
		switch($this->OS) {
			case 'Windows':
				passthru('shutdown /r');
			break;
			case 'Linux':
				passthru('/sbin/reboot');
			break;
		}
	}
	
	public function standby() {
		switch($this->OS) {
			case 'Windows':
				passthru('powercfg -h off');
				passthru('rundll32.exe powrprof.dll,SetSuspendState 0,1,0');
				passthru('powercfg -h on');
			break;
			case 'Linux':
			break;
		}
	}
	
	
	public function createFile($path, $size) {
		switch($this->OS) {
			case 'Windows':
				return $this->executeCommand('fsutil file createnew "' . $path . '" ' . $size);
			break;
			case 'Linux':
				passthru('truncate -s ' . $size . ' ' . $path);
				//passthru('fallocate -l ' . $size . ' ' . $path);
			break;
		}
	}
	
	public function executeCommand($command) {
		ob_start();
		passthru($command, $response);
		$message = ob_get_contents();
		ob_end_clean();
		return array($response, $message);
	}
}

$computerController = new ComputerController();
?>