<?php
	$cli = new \Swoole\Client(SWOOLE_SOCK_TCP);
	$cli->connect('127.0.0.1', 9906, 1);
	fwrite(STDOUT, 'Enter command:');
	$msg = trim(fgets(STDIN));
	$cli->send($msg);
	echo $msg.' sended'.PHP_EOL;
?>