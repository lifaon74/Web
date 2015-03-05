<?php
require_once('SocketClient.class.php');

class ASyncSocketClient extends SocketClient {

	protected $socketClient;
	
	function __construct() {
	}
	
	
/**
*	Public methods
**/
	public function connect() {	
		$this->trigger('connect', [$this]);
	}
	
	public function update() {
	}
	
	public function send($data) {
	}
	
	public function disconnect() {
		$this->trigger('disconnect', [$this]);
	}
	
}

?>