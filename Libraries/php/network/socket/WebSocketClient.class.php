<?php
require_once('SocketClient.class.php');
require_once(dirname(__FILE__) . '/../../Converter.class.php');


class WebSocketPacket {
	public $opcode = 0;
	public $isMasked = 0;
	public $dataLength = 0;
	public $mask = [0, 0, 0, 0];
	
	public $_payloadLength = 0;
	public $_dataLengthOffset = 0;
	public $_maskOffset = 0;
	public $_dataOffset = 0;
	
	public $data = "";
}


class WebSocketClient extends SocketClient {

		// Public readonly attributes
	public 		$handshaked = false;
	public		$indisconnection = false;
		
		// Protected attributes
	protected 	$_eol = "\r\n";
	
		// Private attributes
	private		$_webSocketPacket = null;
	
	private 	$_opcodeMask		= 0b00001111;
	private 	$_maskMask 			= 0b10000000;
	private 	$_dataLengthMask 	= 0b01111111;
	private 	$_responseMask 		= 0b10000000;
	
	private 	$_step = 0;
	
	
	
	function __construct($socket) {
		parent::__construct($socket);
	}

	
/**
*	Public methods
**/
	public function connect() {
		parent::connect();
	}
	
	public function update() {
		parent::update();
	}
	
	public function send($data) {
		parent::send($data);
	}
	
	public function disconnect() {
		parent::disconnect();
	}
	

	
/**
*	Protected methods
**/
	protected function decode($data) {
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

	protected function encode($data, $opcode = 1, $masked = false) {
		global $Converter;
		
		$dataLength = strlen($data);
		
		$packet = [];
		$i = 0;
		
			// byte 0
		$packet[$i] = $this->_responseMask + ($this->_opcodeMask & $opcode);
		$i++;
		
		if($dataLength >= 256 * 256) {
			$length = 127;
			$sizeBytes = $Converter->intToChar($dataLength, 8);
		} else if($dataLength > 125) {
			$length = 126;
			$sizeBytes = $Converter->intToChar($dataLength, 2);
		} else {
			$length = $dataLength;
			$sizeBytes = [];
		}
		
			// byte 1
		$packet[$i] = $length & $this->_dataLengthMask;
		if($masked) { $packet[$i] += $this->_maskMask; }
		$i++;
		
			// bytes 2->8
		for($j = 0; $j < count($sizeBytes); $j++) {
			$packet[$i] = $sizeBytes[$j];
			$i++;
		}
		
			// mask
		if($masked) {
			$mask = [];
			for($j = 0; $j < 4; $j++) {
				$mask[$j] = rand(0, 255);
				$packet[$i] = $mask[$j];
				$i++;
			}			
		}
		
			// put data
		for($j = 0; $j < $dataLength; $j++) {
			if($masked) {
				$packet[$i] = ord($data[$j]) ^ $mask[$j % 4];
			} else {
				$packet[$i] = ord($data[$j]);
			}
			$i++;
		}
		
		return $Converter->charToString($packet);
	}
	
		protected function _onreceivedata($data) {
		}

		
/**
*	Private methods
**/
		// start new packet, read opcode
	private function _step0($byte) {
		$this->_webSocketPacket = new WebSocketPacket();
		$this->_webSocketPacket->opcode = $byte & $this->_opcodeMask;
		
		//$this->log("opcode : " . $this->_webSocketPacket->opcode, 0);
		$this->_step = 1;
	}
	
		// read length
	private function _step1($byte) {
		$this->_webSocketPacket->isMasked = ($byte & $this->_maskMask) && $this->_maskMask;
		$this->_webSocketPacket->_payloadLength = $byte & $this->_dataLengthMask;
		
		switch($this->_webSocketPacket->_payloadLength) {
			case 126:
			case 127:
				$this->_step = 2;
			break;
			default:
				$this->_webSocketPacket->dataLength = $this->_webSocketPacket->_payloadLength;
				if($this->_webSocketPacket->isMasked) {
					$this->_step = 3;
				} else {
					if($this->_webSocketPacket->dataLength > 0) {
						$this->_step = 4;
					} else {
						$this->_step5();
					}
				}
			break;
		}
		
		//$this->log("masked : " . $this->_webSocketPacket->isMasked, 0);
		//$this->log("payload length : " . $this->_webSocketPacket->_payloadLength, 0);
	}
	
		// read extended length
	private function _step2($byte) {
		switch($this->_webSocketPacket->_payloadLength) {
			case 126:
				$nbByte = 2;
			break;
			case 127:
				$nbByte = 8;
			break;
			
		}
		
		$this->_webSocketPacket->dataLength = $this->_webSocketPacket->dataLength * 256 + $byte;
		
		$this->_webSocketPacket->_dataLengthOffset++;
		if($this->_webSocketPacket->_dataLengthOffset >= $nbByte) {
			//$this->log("length : " . $this->_webSocketPacket->dataLength, 0);
			
			if($this->_webSocketPacket->isMasked) {
				$this->_step = 3;
			} else {
				if($this->_webSocketPacket->dataLength > 0) {
					$this->_step = 4;
				} else {
					$this->_step5();
				}
			}
		}
	}
	
		// read mask
	private function _step3($byte) {
		$this->_webSocketPacket->mask[$this->_webSocketPacket->_maskOffset] = $byte;
		
		$this->_webSocketPacket->_maskOffset++;
		if($this->_webSocketPacket->_maskOffset >= 4) {
			//$this->log("mask : " . $this->_webSocketPacket->mask[0] . ", " . $this->_webSocketPacket->mask[1] . ", " . $this->_webSocketPacket->mask[2] . ", " . $this->_webSocketPacket->mask[3], 0);
			
			if($this->_webSocketPacket->dataLength > 0) {
				$this->_step = 4;
			} else {
				$this->_step5();
			}
		}
	}
		
		// read data
	private function _step4($byte) {
		$byte = $byte ^ $this->_webSocketPacket->mask[$this->_webSocketPacket->_dataOffset % 4];
		
		$this->_webSocketPacket->data .= chr($byte);
		
		$this->_webSocketPacket->_dataOffset++;
		if($this->_webSocketPacket->_dataOffset >= $this->_webSocketPacket->dataLength) {
			$this->_step5();
		}
	}
	
		// packet complete
	private function _step5() {
		$this->_step = 0;
		//$this->log("packet complete", 0);
		
		switch($this->_webSocketPacket->opcode) {
			case 1: // text
				$this->_onreceivedata($this->_webSocketPacket->data);
			break;
			case 8: // close
				if(!$this->indisconnection) { // client ask for deconnection
					parent::send($this->_encode('normal closure', 8, false));
				}
				
				parent::disconnect();
				$this->handshaked = false;
				$this->indisconnection = false;
			break;
			case 9: // ping	
			break;
			case 10: // pong
			break;	
		}
	}
	
}

?>