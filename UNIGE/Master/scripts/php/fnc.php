<?php

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

switch($_SERVER['HTTP_HOST']) {
	case 'localhost':
	case '78.244.106.44':
		$fnc = new Fnc('E:\wamp\www\Web\UNIGE\Master');
	break;
	case 'thingbook.valentin-richard.com':
		$fnc = new Fnc('/home/lifaon74/public_html/thingbook/UNIGE/Master');
	break;
}
?>