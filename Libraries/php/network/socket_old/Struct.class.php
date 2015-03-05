<?php
require_once(dirname(__FILE__) . '/../../Binds.class.php');

class Struct extends Binds {
	public function _log($message) {
		echo '[' . date('d-m-Y H:i:s') . '] : ' . $message . PHP_EOL;
	}
	
	public function _error($message) {
		die($message);
	}
}

?>