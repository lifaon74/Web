<?php
require_once('WebSocketClient.class.php');

class WebSocketClientServerSide extends WebSocketClient {

		// Private attributes
	private		$_handshakeHeader = "";
	private 	$_key = "";
	
	
	function __construct($socket) {
		parent::__construct($socket);
	}
	
	
/**
*	Public methods
**/
	public function connect() {
		if(!$this->connected) {
			parent::connect();
		}
	}
	
	public function update() {
		if($this->connected) {
			$data = parent::readBuffer();
			
			if($data === null) {
				$this->disconnect();
			} else {
					// receive some data
				if($this->handshaked) {
					$this->decode($data);
				} else {
					$this->_handshakeHeader .= $data;
					
					for($i = strlen($this->_handshakeHeader); $i >= 3; $i--) {
						if(
							$this->_handshakeHeader[$i - 3] == "\r" &&
							$this->_handshakeHeader[$i - 2] == "\n" &&
							$this->_handshakeHeader[$i - 1] == "\r" &&
							$this->_handshakeHeader[$i - 0] == "\n"
						) {
							$this->log("handshake received", 0);
							$this->_analyseHandshakeHeader($this->_handshakeHeader);
						}
					}
				}
			}
		}
	}
	
	public function send($data) {
		if($this->handshaked) {
			parent::send($this->encode($data, 1, false));
		}
	}
	
	public function disconnect() {
		if($this->handshaked) {
			if(!$this->indisconnection) {
				$this->indisconnection = true;
				parent::send($this->encode('normal closure', 8, false));
			}
		}
	}


/**
*	Protected methods
**/

	protected function _onreceivedata($data) {
		$this->trigger('receive', [$data, $this]);
	}
	

/**
*	Private methods
**/
	private function _analyseHandshakeHeader($handshakeHeader) {
		if(preg_match('#^GET (\S+) HTTP\/1.1' . $this->_eol . '#', $handshakeHeader, $matches)) { // http request
			$path = substr($matches[1], 1);
			
			$headers = array();
			$lines = preg_split('#' . $this->_eol . '#', $handshakeHeader);
			
			foreach($lines as $line) {
				$line = chop($line);
				if(preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
					$headers[$matches[1]] = $matches[2];
				}
			}
			
			if(
				isset($headers['Upgrade']) && $headers['Upgrade'] == 'websocket' &&
				isset($headers['Connection']) && $headers['Connection'] == 'Upgrade' &&
				isset($headers['Sec-WebSocket-Key'])
			) {
				$this->log('Client has a valid handshake', 0);
				
				$this->_sendHandshakeResponse($headers);
				$this->trigger('connect', [$this]);
			}
		} else {
			$this->log('Client has not a valid handshake', 5);
		}
	}
	
	private function _sendHandshakeResponse($headers) {
		$this->_key = $headers['Sec-WebSocket-Key'];
	
		$responseKey = base64_encode(pack('H*', sha1($this->_key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
		
		$response = "HTTP/1.1 101 Switching Protocols" . $this->_eol;
		$response .= "Upgrade: websocket" . $this->_eol;
		$response .= "Connection: Upgrade" . $this->_eol;
		$response .= "Sec-WebSocket-Accept: " . $responseKey . $this->_eol;
		
		if(isset($headers['Sec-WebSocket-Protocol'])) {
			$response .= "Sec-WebSocket-Protocol: " . $headers['Sec-WebSocket-Protocol'] . $this->_eol;
		}
		
		$response .= $this->_eol;
		
		parent::send($response);
	
		$this->handshaked = true;
	}
	
}

?>