<?php

class WebSocketPacket {
	public $opcode = 0;
	public $isMasked = 0;
	public $dataLength = 0;
	public $mask = [0, 0, 0, 0];
	public $type = null;
	
	public $_payloadLength = 0;
	public $_dataLengthOffset = 0;
	public $_maskOffset = 0;
	public $_dataOffset = 0;
	
	public $data = "";
}


class WebSocket extends Struct {
	
	public $handshaked = false;
	
	private $client = null;
	private $eol = "\r\n";
	
	
	
	private $_opcodeMask = 0b00001111;
	private $_maskMask = 0b10000000;
	private $_dataLengthMask = 0b01111111;
	
	private $_step = 0;
	
	
	function __construct($client) {
		$this->client = $client;
	}
	
	
	public function handShake($headers) {
		if(isset($headers['Sec-WebSocket-Protocol'])) {
			$this->protocol = $headers['Sec-WebSocket-Protocol'];
		}
		
		$this->key = $headers['Sec-WebSocket-Key'];
	
		$secAccept = base64_encode(pack('H*', sha1($this->key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
		$response = "HTTP/1.1 101 Switching Protocols" . $this->eol;
		$response .= "Upgrade: websocket" . $this->eol;
		$response .= "Connection: Upgrade" . $this->eol;
		$response .= "Sec-WebSocket-Accept: " . $secAccept . $this->eol;
		if(isset($this->protocol)) {
			$response .= "Sec-WebSocket-Protocol: " . $this->protocol . $this->eol;
		}
		
		$response .= $this->eol;
		
		$this->client->_send($response);
		
		$handshaked = true;
	}
	
	public function send($data) {
		return $this->_encode($data);
	}
	
	public function receive($data) {
		$decodedData = $this->_decode($data);
		return null;
	}
	
	public function disconnect() {
		$data = 'normal closure';
		$data = $this->_encode($data, 'close');
		$this->client->_send($data);
	}
	
	private function _receiveWebSocketPacket($webSocketPacket) {
		switch($webSocketPacket->type) {
			case 'text':
				$this->client->_trigger('receive', [$webSocketPacket->data]);
			break;			
			case 'ping':
				$data = $this->_encode($webSocketPacket->data, 'pong');
				$this->client->_send($data);
				$this->_log("Ping? Pong!");
			break;
			case 'pong':
				// server currently not sending pings, so no pong should be received.
			break;
			case 'close':
				$this->client->disconnect();
			break;
		}
	}
	
	
	private function _encode($data, $type = 'text') {
		$masked = false;
		$frameHead = array();
		$frame = '';
		$dataLength = strlen($data);
		
		switch($type) {		
			case 'text':
				$frameHead[0] = 129;				
			break;			
			case 'close':
				$frameHead[0] = 136;
			break;
			case 'ping':
				$frameHead[0] = 137;
			break;
			case 'pong':
				$frameHead[0] = 138;
			break;
		}
		
		if($dataLength > 65535) {
			$dataLengthBin = str_split(sprintf('%064b', $dataLength), 8);
			$frameHead[1] = ($masked === true) ? 255 : 127;
			for($i = 0; $i < 8; $i++) {
				$frameHead[$i+2] = bindec($dataLengthBin[$i]);
			}
			
			if($frameHead[2] > 127) {
				$this->_error(3);
				return false;
			}
		} else if($dataLength > 125) {
			$dataLengthBin = str_split(sprintf('%016b', $dataLength), 8);
			$frameHead[1] = ($masked === true) ? 254 : 126;
			$frameHead[2] = bindec($dataLengthBin[0]);
			$frameHead[3] = bindec($dataLengthBin[1]);
		} else {
			$frameHead[1] = ($masked === true) ? $dataLength + 128 : $dataLength;
		}

		foreach(array_keys($frameHead) as $i) {
			$frameHead[$i] = chr($frameHead[$i]);
		}
		
		if($masked === true) {
			$mask = array();
			for($i = 0; $i < 4; $i++)
			{
				$mask[$i] = chr(rand(0, 255));
			}
			
			$frameHead = array_merge($frameHead, $mask);			
		}		
		
		$frame = implode('', $frameHead);

		for($i = 0; $i < $dataLength; $i++) {		
			$frame .= ($masked === true) ? $data[$i] ^ $mask[$i % 4] : $data[$i];
		}

		return $frame;
	}
	
	
	private function _decode($data) {
		//echo "datalength : " . strlen($data) . "\n";
		for($i = 0; $i < strlen($data); $i++) {
			$byte = ord($data[$i]);
			switch($this->_step) {
				case 0: // read opcode
					$this->_step0($byte);
				break;
				case 1: // read mask and dataLength
					$this->_step1($byte);
				break;
				case 2: // read big dataLength
					$this->_step2($byte);
				break;
				case 3: // read mask
					$this->_step3($byte);
				break;
				case 4: // read data
					$this->_step4($byte);
				break;
			}
		}
	}

	
	private function _step0($byte) {
		$this->webSocketPacket = new WebSocketPacket();
		$this->webSocketPacket->opcode = $byte & $this->_opcodeMask;
		
			// get type
		switch($this->webSocketPacket->opcode) {
			case 1:
				$this->webSocketPacket->type = 'text';				
			break;
			case 8:
				$this->webSocketPacket->type = 'close';
			break;
			case 9:
				$this->webSocketPacket->type = 'ping';				
			break;
			case 10:
				$this->webSocketPacket->type = 'pong';
			break;
			default:
				$this->_error(2);
			break;
		}
		
		
		//$this->_log("opcode : " . $this->webSocketPacket->opcode);
		$this->_step = 1;
	}
	
	private function _step1($byte) {
		$this->webSocketPacket->isMasked = ($byte & $this->_maskMask) && $this->_maskMask;
		$this->webSocketPacket->_payloadLength = $byte & $this->_dataLengthMask;
		
		switch($this->webSocketPacket->_payloadLength) {
			case 126:
			case 127:
				$this->_step = 2;
			break;
			default:
				$this->webSocketPacket->dataLength = $this->webSocketPacket->_payloadLength;
				if($this->webSocketPacket->isMasked) {
					$this->_step = 3;
				} else {
					if($this->webSocketPacket->dataLength > 0) {
						$this->_step = 4;
					} else {
						$this->_step5();
					}
				}
			break;
		}
		
		//$this->_log("masked : " . $this->webSocketPacket->isMasked);
		//$this->_log("payload length : " . $this->webSocketPacket->_payloadLength);
	}
	
	private function _step2($byte) {
		switch($this->webSocketPacket->_payloadLength) {
			case 126:
				$nbByte = 2;
			break;
			case 127:
				$nbByte = 8;
			break;
			
		}
		
		$this->webSocketPacket->dataLength = $this->webSocketPacket->dataLength * 256 + $byte;
		
		$this->webSocketPacket->_dataLengthOffset++;
		if($this->webSocketPacket->_dataLengthOffset >= $nbByte) {
			$this->_log("length : " . $this->webSocketPacket->dataLength);
			
			if($this->webSocketPacket->isMasked) {
				$this->_step = 3;
			} else {
				if($this->webSocketPacket->dataLength > 0) {
					$this->_step = 4;
				} else {
					$this->_step5();
				}
			}
		}
	}
	
	private function _step3($byte) {
		$this->webSocketPacket->mask[$this->webSocketPacket->_maskOffset] = $byte;
		
		$this->webSocketPacket->_maskOffset++;
		if($this->webSocketPacket->_maskOffset >= 4) {
			//$this->_log("mask : " . $this->webSocketPacket->mask[0] . ", " . $this->webSocketPacket->mask[1] . ", " . $this->webSocketPacket->mask[2] . ", " . $this->webSocketPacket->mask[3]);
			
			if($this->webSocketPacket->dataLength > 0) {
				$this->_step = 4;
			} else {
				$this->_step5();
			}
		}
	}
		
	private function _step4($byte) {
		$byte = $byte ^ $this->webSocketPacket->mask[$this->webSocketPacket->_dataOffset % 4];
		
		$this->webSocketPacket->data .= chr($byte);
		
		$this->webSocketPacket->_dataOffset++;
		if($this->webSocketPacket->_dataOffset >= $this->webSocketPacket->dataLength) {
			//$this->_log("data : " . $this->webSocketPacket->data);
			$this->_step5();
		}
	}
	
	private function _step5() { // packet is complete
		$this->_step = 0;
		//$this->_log("packet complete");
		$this->_receiveWebSocketPacket($this->webSocketPacket);
	}
	
	
	public function _error($statusCode) {
	
		$errors = array();
		$errors[0] = '';
		$errors[1] = "protocol error";
		$errors[2] = 'unknown opcode';
		$errors[3] = 'frame too large';
		
		$error = $errors[$statusCode];
		
		$data = $this->_encode($error, 'close');
		$this->client->_send($data);
		
		$message = "WebSocket : " . $error;
		
		$this->_log($message);
	}
}

?>