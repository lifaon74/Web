<?php
/********************************************************************************/
/*	Author : Richard Valentin													*/
/*																				*/
/*	new SocketClient([, $host, $port]) : create a socket client					*/
/*		connect([, $host, $port]) : connect client to the server				*/
/*		disconnect(): disconnect client from the server							*/
/*		send($data) : send $data												*/
/*																				*/
/*	Binds :																		*/
/*		loop($elapsedTime, $client) : fired every $_loopInterval milliseconds	*/
/*		receive($data) : fired when $data are received							*/
/*		disconnect() : fired when the client disconnects						*/
/*																				*/
/********************************************************************************/


require_once('Struct.class.php');

if(!defined('MSG_DONTWAIT')) {
	define('MSG_DONTWAIT', 0x40);
}


class SocketClient extends Struct {

		// Public readonly attributes
	public 		$hostIP = '127.0.0.1';
	public 		$hostPort = 12800;
	
	public 		$localIP = '127.0.0.1';
	public 		$localPort = 12800;

		// Parameters
	private 	$_timeout = 50; // 50us
	private 	$_loopInterval = 1000;
	private 	$_bufferLength = 4096;

		// Private attributes
	protected 	$_server = null;
	protected 	$_socket;
	protected 	$_connected = false;
	private 	$_loopLastTimestamp = 0;
	
	private 	$dataType = null; // raw, websocket
	private 	$eol = "\r\n";
	
	
	function __construct($server = null, $socket = null) {
		if($server && is_object($server)) {
			$this->_connected = true;
			
			$this->_server = $server;
			$this->_socket = $socket;
			
			socket_getpeername($this->_socket, $ip, $port);
			$this->localIP = $ip;
			$this->localPort = $port;
			$this->connectionId = uniqid();
			
			$this->_log('Client connected');
		} else {
			$this->hostIP = $server;
			$this->hostPort = $socket;
		}
	}

	
/**
*	Public methods
**/
	
	public function connect($host = null, $port = null) {
		if($host === null) { $host = $this->hostIP; }
		if($port === null) { $port = $this->hostPort; }
			
		if(!$this->_connected) {
		
			$this->_connected = true;
			
			$this->hostIP = $host;
			$this->hostPort = $port;
			
			echo $this->hostIP;
			
			if(!$this->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) {
				$this->_error("socket_create() failed, reason: " . socket_strerror(socket_last_error()));
			}
			
			if(!socket_connect($this->_socket, $this->hostIP, $this->hostPort)) {
				$this->_error("socket_connect() failed, reason: " . socket_strerror(socket_last_error($this->_socket)));
			}
			
			$this->_log('Client connected');
			
			while($this->_connected) {
			
				$time = microtime(true) * 1000;
				$diff = $time - $this->_loopLastTimestamp;
				if($diff >= $this->_loopInterval) {
					$this->_trigger('loop', [$diff, $this]);
					$this->_loopLastTimestamp = $time;
				}
				
				$changedSockets = [$this->_socket];
				@socket_select($changedSockets, $write = NULL, $except = NULL, 0, $this->_timeout);

				foreach($changedSockets as $socket) {
					if($socket == $this->_socket) {
						$bytes = @socket_recv($this->_socket, $data, $this->_bufferLength, 0);
						
						if(!isset($bytes) || $bytes === false) {
							$this->disconnect();
						} else {
							$this->_receive($data);
						}
					}
				}
			}
		}
	}
	
	public function disconnect() {
		if($this->_connected) {
			switch($this->dataType) {
				case 'raw':
				break;
				case 'websocket':
					$this->webSocket->disconnect();
				break;
			}
			
			socket_close($this->_socket);

			if($this->_server !== null) {
				unset($this->_server->clients[intval($this->_socket)]);
				$index = array_search($this->_socket, $this->_server->_sockets);
				unset($this->_server->_sockets[$index]);
			}
			
			$this->_trigger('disconnect');
			
			$this->_log('Client disconnected');
			
			$this->_connected = false;
		}
	}
	
	public function send($data) {
		if($this->_connected) {
		
			if(!is_string($data)) { $data .= ''; }
			
			switch($this->dataType) {
				case 'raw':
				break;
				case 'websocket':
					$data = $this->webSocket->send($data);
				break;
			}
			
			if($data !== null) {
				$this->_send($data);
			}
		}
	}
	

	
/**
*	Private methods
**/
	
	public function _send($data) {
		if($this->_connected) {
			if(!@socket_write($this->_socket, $data, strlen($data))) {
				//$this->_error("socket_write() failed, reason: " . socket_strerror(socket_last_error($this->_socket)));
				//$this->_error("socket_write() failed, reason: " . socket_last_error($this->_socket));
			}
		}
	}

	public function _receive($data) {
		if($this->dataType === null) {
			$data = $this->_analyseDataType($data);
		} else {
			switch($this->dataType) {
				case 'raw':
				break;
				case 'websocket':
					$data = $this->webSocket->receive($data);
				break;
			}
		}
		
		if($data !== null) {
			$this->_trigger('receive', [$data]);
		}
	}
	
	
	private function _analyseDataType($data) {
		if(preg_match('#^GET (\S+) HTTP\/1.1' . $this->eol . '#', $data, $matches)) { // http request
			
			$path = substr($matches[1], 1);
			
			$headers = array();
			$lines = preg_split('#' . $this->eol . '#', $data);
			
			foreach($lines as $line) {
				$line = chop($line);
				if(preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
					$headers[$matches[1]] = $matches[2];
				}
			}
			
			return $this->_analyseHeaders($headers);
		} else { // raw data
			$this->dataType = 'raw';
			return $data;
		}
		
		return null;
	}
	
	private function _analyseHeaders($headers) {
		if(isset($headers['Upgrade']) && $headers['Upgrade'] == 'websocket') { //websocket
			require_once('WebSocket.class.php');
			
			$this->webSocket = new WebSocket($this);
			$this->webSocket->handShake($headers);
			$this->_log('is a websocket User');
			$this->dataType = 'websocket';
			return null;
		}
		
		return null;
	}

	
	public function _log($message) {
		parent::_log('[' . $this->localIP . ':' . $this->localPort . '] : ' . $message);
	}

	public function _trigger($eventname, $arguments = []) {
		$clientArguments = [];
		$serverArguments = [$this];
		
		for($i = 0; $i < count($arguments); $i++) {
			$clientArguments[] = $arguments[$i];
			$serverArguments[] = $arguments[$i];
		}
		
		$clientArguments[] = $this;
		
		$this->trigger($eventname, $clientArguments);
		
		if($this->_server !== null) {
			$this->_server->trigger($eventname, $serverArguments);
		}
	}

}


?>