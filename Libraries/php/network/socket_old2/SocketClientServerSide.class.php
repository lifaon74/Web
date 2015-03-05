<?php
require_once('SocketClient.class.php');

class SocketClientServerSide extends SocketClient {

	function __construct($socket) {
		parent::__construct($socket);
	}
	
/**
*	Public methods
**/
	public function connect() {
		if(!$this->connected) {
			parent::connect();
			$this->trigger('connect', [$this]);
		}
	}
	
	public function update() {
		if($this->connected) {
			$data = parent::readBuffer();
			
			if($data === null) {
				$this->disconnect();
			} else {
				$this->trigger('receive', [$data, $this]);
			}
		}
	}
	
	public function send($data) {
		parent::send($data);
	}
	
	public function disconnect() {
		if($this->connected) {
			parent::disconnect();
			$this->trigger('disconnect', [$this]);
		}
	}

}

?>