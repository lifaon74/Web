<?php
require_once('Log.class.php');

class SocketClient extends Log {

		// Public readonly attributes
	public 		$clientIP = '';
	public 		$clientPort = 0;
	
	public 		$socket = null;
	public 		$connected = false;
	
		// Parameters
	private 	$_bufferLength = 4096;
	
	
	
	function __construct($socket) {
		$this->socket = $socket;
		
		socket_getpeername($this->socket, $this->clientIP, $this->clientPort);
	}
	
	
/**
*	Public methods
**/
	public function connect() {
		if(!$this->connected) {
			$this->connected = true;
		}
	}
	
	public function update() {
	}
	
	public function send($data) {
		if($this->connected) {
			if(!@socket_write($this->socket, $data, strlen($data))) {
				$this->log("socket_write() failed, reason: " . socket_strerror(socket_last_error($this->socket)), 5);
			}
		}
	}
	
	public function disconnect() {
		if($this->connected) {
			@socket_close($this->socket);
			$this->connected = false;
			$this->log('Client disconnected', 0);
		}
	}


/**
*	Protected methods
**/
	protected function readBuffer() {
		if($this->connected) {
			$bytes = @socket_recv($this->socket, $data, $this->_bufferLength, 0);
			
			if(!isset($bytes) || $bytes === false/* || $bytes === 0*/) {
				return null;
			} else {
				return $data;
			}
		}
	}
}

?>