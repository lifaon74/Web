<?php

$root = '../libs/functions/php/';
require_once($root . 'network/socket/SocketClient.class.php');

$socketClient = new SocketClient('192.168.1.60', 12800);

/*$data = file_get_contents('www/sockets/ws.txt');
$socketClient->_receive($data);*/
$socketClient->bind('loop', function() use(&$socketClient) {
	$socketClient->send('ok');
});

$socketClient->bind('receive', function($data) {
	echo 'Client : ' . $data;
	echo "\n";
});

$socketClient->connect();

?>