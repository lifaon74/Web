<?php
require_once(dirname(__FILE__) . '/../../Binds.class.php');

class Log extends Binds {
	public function log($message, $weight) {
		echo '[' . date('d-m-Y H:i:s') . '] : ' . $message . PHP_EOL;
	}
}

?>