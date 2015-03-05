<?php
require_once('ASyncSocketClient.class.php');

class ASyncSocketClientServerSide extends ASyncSocketClient {

	function __construct($socket) {
		parent::__construct();
		$this->socketClient->linkWithServerSocket($socket);
	}
	
	
/**
*	Public methods
**/
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