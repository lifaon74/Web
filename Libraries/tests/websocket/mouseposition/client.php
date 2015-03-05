<?php

$root = '../libs/functions/php/';
require_once($root . 'network/socket/WebSocketClientClientSide.class.php');

$socketClient = new WebSocketClientClientSide('192.168.1.60', 12800);

$socketClient->bind('connect', function($client) {
	echo "Client connected\n";
	$client->send("start");
});

$socketClient->bind('receive', function($data, $client) {
	echo $data . "\n";
	$client->send("client response");
});

$socketClient->connect();


while(true) {
	$socketClient->update();
	usleep(50 * 1000);
}
?>