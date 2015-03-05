<?php
/********************************************************************************/
/*	Author : Richard Valentin													*/
/*																				*/
/*	new SocketServer($host, $port) : create a socket server						*/
/*		start() : start the server												*/
/*		stop(): stop the server													*/
/*																				*/
/*	Binds :																		*/
/*		connect($client) : fired when a $client connects						*/
/*		receive($data, $client) : fired when $data are received					*/
/*		disconnect($client) : fired when a $client disconnects					*/
/*																				*/
/********************************************************************************/

require_once('Log.class.php');
require_once('SocketClientServerSide.class.php');

class SocketServer extends Log {

		// Public readonly attributes
	public 		$host = "auto";
	public 		$port = 12800;
	public 		$protocol = SOL_TCP;
	public 		$clients = [];
	public		$created = false;
	public 		$started = false;
	
		// Parameters
	private 	$_maxClients = 10;
	private 	$_timeout = 0; // 50us
	private 	$_bufferLength = 4096;
	
		// Private attributes
	private 	$_masterSocket = null;
	private 	$_sockets = [];
	private 	$_loopLastTimestamp = 0;
	
	
	function __construct($host = null, $port = null) {
		if($host === null) { $host = $this->host; }
		if($port === null) { $port = $this->port; }
		if($host == "auto") { $host = gethostbyname(gethostname()); }
		
		$this->host = $host;
		$this->port = $port;
	}
	

/**
*	Public methods
**/
	public function create() {
		if(!$this->created) {
			if(!$this->_masterSocket = @socket_create(AF_INET, SOCK_STREAM, $this->protocol)) {
				$this->log("socket_create() failed, reason: " . socket_strerror(socket_last_error()), 10);
				return;
			}
			
			socket_set_option($this->_masterSocket, SOL_SOCKET, SO_REUSEADDR, 1);
			
			if(!@socket_bind($this->_masterSocket, $this->host, $this->port)) {
				$this->log("socket_bind(" . $this->host . ":" . $this->port . ") failed, reason: " . socket_strerror(socket_last_error($this->_masterSocket)), 10);
				return;
			}
			
			if(!@socket_listen($this->_masterSocket, $this->_maxClients)) {
				$this->log("socket_listen() failed, reason: " . socket_strerror(socket_last_error($this->_masterSocket)), 10);
				return;
			}
			
			$this->_sockets[] = $this->_masterSocket;
			
			$this->log('Server created : ' . $this->host . ':' . $this->port, 0);
			$this->created = true;
		}
	}

	public function start() {
		if(!$this->started) {
			$this->create();
			$this->started = true;
			
			while($this->started) {
				$this->update();
				usleep(1000);
			}
		}
	}
	
	
	public function update() {
		if($this->created) {
			$changedSockets = $this->_sockets;
			@socket_select($changedSockets, $write = NULL, $except = NULL, 0, $this->_timeout);

			foreach($changedSockets as $socket) {
				if($socket == $this->_masterSocket) {
					$this->_acceptClient();
				} else {
					$client = $this->clients[intval($socket)];
					$client->update();
				}		
			}
		}
	}
	
	
	public function stop() {
		if($this->started) {
			$this->started = false;
			$this->destroy();
		}
	}
	
	public function destroy() {
		if($this->created) {
			foreach($this->clients as $client) {
				$client->disconnect();
			}
			
			socket_close($this->_masterSocket);
			$this->_sockets = [];
			
			$this->created = false;
		}
	}

	

/**
*	Protected methods
**/
	protected function _onnewclient($socket) {
		return new SocketClientServerSide($socket);
	}
	

/**
*	Private methods
**/
	private function _acceptClient() {
		if(($socket = socket_accept($this->_masterSocket)) < 0) {
			$this->log('Socket error: ' . socket_strerror(socket_last_error($socket)), 5);
		} else {
			$client = $this->_onnewclient($socket);
			
			$this->clients[intval($socket)] = $client;
			$this->_sockets[] = $socket;
			
			$self = $this;
			$client->bind('connect', function($client) use(&$self) {
				$this->trigger('connect', [$client, $self]);
			});
			
			$client->bind('disconnect', function($client) {
				unset($this->clients[intval($client->socket)]);
				
				$index = array_search($client->socket, $this->_sockets);
				unset($this->_sockets[$index]);
			});
			
			$client->connect();
		}
	}

}
?>