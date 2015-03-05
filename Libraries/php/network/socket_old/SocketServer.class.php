<?php
/********************************************************************************/
/*	Author : Richard Valentin													*/
/*																				*/
/*	new SocketServer($host, $port) : create a socket server						*/
/*		start() : start the server												*/
/*		stop(): stop the server													*/
/*		broadcast($data) : send $data to all connected clients					*/
/*																				*/
/*	Binds :																		*/
/*		loop($elapsedTime, $server) : fired every $_loopInterval milliseconds	*/
/*		connect($client) : fired when a $client connects						*/
/*		receive($data, $client) : fired when $data are received					*/
/*		disconnect($client) : fired when a $client disconnects					*/
/*																				*/
/********************************************************************************/

require_once('Struct.class.php');
require_once('SocketClient.class.php');

class SocketServer extends Struct {

		// Public readonly attributes
	public 		$host = '127.0.0.1';
	public 		$port = 12800;
	public 		$clients = [];
	public 		$_sockets = [];
	
		// Parameters
	private 	$_maxClients = 10;
	private 	$_timeout = 50; // 50us
	private 	$_loopInterval = 1000;
	protected 	$_bufferLength = 4096;
	
		// Private attributes
	protected 	$_masterSocket;
	protected 	$_started = false;
	private 	$_loopLastTimestamp = 0;
	
	
	function __construct($host = null, $port = null) {
		if($host === null) { $host = $this->host; }
		if($port === null) { $port = $this->port; }
	
		$this->host = $host;
		$this->port = $port;
	}
	

/**
*	Public methods
**/
	public function start() {
		if(!$this->_started) {
			$this->_started = true;
			
			$this->_createSocket();
			
			while($this->_started){
			
				$time = microtime(true) * 1000;
				$diff = $time - $this->_loopLastTimestamp;
				if($diff >= $this->_loopInterval) {
					$this->trigger('loop', [$diff, $this]);
					$this->_loopLastTimestamp = $time;
				}
				
				
				$changedSockets = $this->_sockets;
				@socket_select($changedSockets, $write = NULL, $except = NULL, 0, $this->_timeout);

				foreach($changedSockets as $socket) {
					if($socket == $this->_masterSocket) {
						if (($ressource = socket_accept($this->_masterSocket)) < 0) {
							$this->_error('Socket error: ' . socket_strerror(socket_last_error($ressource)));
							continue;
						} else {
							$client = new SocketClient($this, $ressource);					
							$this->clients[intval($ressource)] = $client;
							$this->_sockets[] = $ressource;
							$this->trigger('connect', [$client]);
						}
					} else {
						$client = $this->clients[intval($socket)];
						
						$disconnected = false;
					
						$bytes = @socket_recv($socket, $data, $this->_bufferLength, 0);
							
						if(!isset($bytes) || $bytes === false || $bytes === 0) {
							$disconnected = true;
						}
						
						if($disconnected) {
							$client->disconnect();
							unset($client);
						} else {
							$client->_receive($data);
						}
					}		
				}
			}
		}
	}
	
	public function stop() {
		if($this->_started) {

			foreach($this->clients as $client) {
				$client->disconnect();
			}
			
			socket_close($this->_masterSocket);
			$this->_sockets = [];
			
			$this->_started = false;
		}
	}
	
	public function broadCast($data) {
		foreach($this->clients as $key => $client) {
			$client->send($data);
		}
	}
	

/**
*	Private methods
**/
	private function _createSocket() {
		if(!$this->_masterSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) {
			$this->_error("socket_create() failed, reason: " . socket_strerror(socket_last_error()));
		}
		
		socket_set_option($this->_masterSocket, SOL_SOCKET, SO_REUSEADDR, 1);
		
		if(!socket_bind($this->_masterSocket, $this->host, $this->port)) {
			$this->_error("socket_bind() failed, reason: " . socket_strerror(socket_last_error($this->_masterSocket)));
		}
		
		if(!socket_listen($this->_masterSocket, $this->_maxClients)) {
			$this->_error("socket_listen() failed, reason: " . socket_strerror(socket_last_error($this->_masterSocket)));
		}
		
		$this->_sockets[] = $this->_masterSocket;
		
		$this->_log('Server started : ' . $this->host . ':' . $this->port);
	}
	
}
?>