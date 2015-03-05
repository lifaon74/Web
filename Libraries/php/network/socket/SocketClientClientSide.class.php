<?php
require_once('SocketClient.class.php');

class SocketClientClientSide extends SocketClient {
	
		// Public readonly attributes
	public 		$hostIP = '';
	public 		$hostPort = 0;
	public 		$protocol = SOL_TCP;
	
		// Parameters
	private 	$_timeout = 0;
	
	function __construct($host = null, $port = null) {
		if($host === null) { $host = $this->hostIP; }
		if($port === null) { $port = $this->hostPort; }
		if($host == "auto") { $host = gethostbyname(gethostname()); }
		
		$this->hostIP = $host;
		$this->hostPort = $port;
	}
	

/**
*	Public methods
**/
	public function connect() {
		if(!$this->connected) {
			
			if(!$socket = @socket_create(AF_INET, SOCK_STREAM, $this->protocol)) {
				$this->log("socket_create() failed, reason: " . socket_strerror(socket_last_error()), 10);
				return;
			}
			
			if(!@socket_connect($socket, $this->hostIP, $this->hostPort)) {
				$this->log("socket_connect() failed, reason: " . socket_strerror(socket_last_error($socket)), 10);
				$this->disconnect();
				return;
			}
			
			parent::__construct($socket);
			parent::connect();
			$this->trigger('connect', [$this]);
		}
	}
	
	public function update() {
		if($this->connected) {
			$changedSockets = [$this->socket];
			@socket_select($changedSockets, $write = NULL, $except = NULL, 0, $this->_timeout);

			foreach($changedSockets as $socket) {
				if($socket == $this->socket) {
					$data = parent::readBuffer();
					
					if($data === null) {
						$this->disconnect();
					} else {
						$this->trigger('receive', [$data, $this]);
					}
				}
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