<?php

include(__DIR__ . '/../../config.php');

class Fnc {
	public $projectRoot;
	public $relativeProjectRoot;
	
	public function __construct($projectRoot = null) {
		if($projectRoot === null) { $projectRoot = $_SERVER['DOCUMENT_ROOT']; }
		
		$this->projectRoot			= $this->formatPath($projectRoot, true);
		$this->relativeProjectRoot	= $this->getRelativeProjectRoot();
		define('ROOT', $this->relativeProjectRoot);
	}
	
	public function formatPath($path, $isDir = false) {
		$path = preg_replace('#\\\\#sU', "/", $path);
		if($isDir && ($path[strlen($path) - 1] != "/")) { $path .= "/"; }
		return $path;
	}
	
	function getRelativeProjectRoot() {
		$caller = $this->formatPath(get_included_files()[0]);

		$callerPath = preg_replace('#' . preg_quote($this->projectRoot) . '#sU', '', $caller);

		$callerPathSplitted = explode("/", $callerPath);
		
		$relativeProjectRoot = "";
		for($i = 0; $i < count($callerPathSplitted) - 1; $i++) {
			$relativeProjectRoot .= "../";
		}
		
		return $relativeProjectRoot;
	}
	
	function absolutePath($path) {
		return $this->relativeProjectRoot . $path;
	}

	function tab($string) {
		return "\t" . preg_replace('#(\n\r?)#sU', "$1\t", $string);
	}
}

$fnc = new Fnc($_CONST_ROOT_PATH);
?>