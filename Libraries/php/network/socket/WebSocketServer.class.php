<?php
require_once('SocketServer.class.php');
require_once('WebSocketClientServerSide.class.php');

class WebSocketServer extends SocketServer {

/**
*	Protected methods
**/
	protected function _onnewclient($socket) {
		return new WebSocketClientServerSide($socket);
	}
}
?>