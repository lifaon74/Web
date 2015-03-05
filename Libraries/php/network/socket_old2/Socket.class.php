<?php
require_once('Log.class.php');

class Socket extends Log {

	
		// Public readonly attributes
	public 		$hostIP = '';
	public 		$hostPort = 0;
	
	public 		$clientIP = '';
	public 		$clientPort = 0;
	
	public 		$socket = null;
	public 		$connected = false;
	
	
		// Parameters
	private 	$_bufferLength = 4096;
	private 	$_protocol = SOL_TCP;
	

/**
*	Public methods
**/
	public function linkToServerSocket($socket) {
		if(!$this->connected) {
			$this->socket = $socket;
			socket_getpeername($this->socket, $this->clientIP, $this->clientPort);
			$this->connected = true;
		}
	}
	
	public function connectToHost($host, $port) {
		if(!$this->connected) {
			if($host == "auto") { $host = gethostbyname(gethostname()); }
		
			$this->hostIP = $host;
			$this->hostPort = $port;
			
			if(!$this->socket = @socket_create(AF_INET, SOCK_STREAM, $this->_protocol)) {
				$this->log("socket_create() failed, reason: " . socket_strerror(socket_last_error()), 10);
				return;
			}
			
			if(!@socket_connect($this->socket, $this->hostIP, $this->hostPort)) {
				$this->log("socket_connect() failed, reason: " . socket_strerror(socket_last_error($this->socket)), 10);
				return;
			}
			
			$this->connected = true;
		}
	}
	
	public function read() {
		if($this->connected) {
			$bytes = @socket_recv($this->socket, $data, $this->_bufferLength, 0);
			
			if(!isset($bytes) || $bytes === false/* || $bytes === 0*/) {
				return null;
			} else {
				return $data;
			}
		}
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
}

?>