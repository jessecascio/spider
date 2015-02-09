<?php

use Spider\Web;
use Spider\Connection;
use Spider\Storage;

error_reporting(-1);

/*
	
	$m->set('poo', 'happy');
	
	echo $m->get('poo');

	$m->flush();

	

	var_dump($m->getAllKeys());

	pkill php
	ps -ef | grep php	

	$s = serialize($Driver);

	unserialize($s);
*/

require __DIR__ . "/vendor/autoload.php";

$m = new Memcached();
$m->addServer('127.0.0.1', 11211);

var_dump($m->get('a12984f7e3c5f4c71795a6430c5884b2_22'));

die();

foreach ($m->getAllKeys() as $key) {
	// $m->delete($key);continue;
	echo "KEY: " . $key . PHP_EOL;
	var_dump(count($m->get($key)));
	echo PHP_EOL;
}