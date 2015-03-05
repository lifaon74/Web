<?php

$root = '../libs/functions/php/';
require_once($root . 'network/socket/SocketServer.class.php');
require_once($root . 'Converter.class.php');

$socketServer = new SocketServer("auto", 12800);

$socketServer->bind('connect', function($client, $server) {

	$client->bind('receive', function($data, $client) {
		echo $data . "\n";
		$client->send("server response");
		//$client->disconnect();
	});
	
	$client->bind('disconnect', function($client) {
	});
});

$socketServer->start();
?>