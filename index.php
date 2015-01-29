<?php

use Spider\Web;
use Spider\Connection;

error_reporting(-1);

/*
	$m = new Memcached();
	$m->addServer('127.0.0.1', 11211);
	$m->set('poo', 'happy');
	
	echo $m->get('poo');

	$m->flush();

	foreach ($m->getAllKeys() as $key) {
		$m->delete($key);
	}

	var_dump($m->getAllKeys());

	pkill php
	ps -ef | grep php	

	$s = serialize($Driver);

	unserialize($s);
*/

require __DIR__ . "/vendor/autoload.php";

$qs = [];
$qs['emp'] = "SELECT * from page LIMIT 5000";
$qs['emp3'] = "SELECT * from page LIMIT 5000";
$qs['emp5'] = "SELECT * from page LIMIT 5000";
$qs['em3p'] = "SELECT * from page LIMIT 5000";
$qs['e3mp'] = "SELECT * from page LIMIT 5000";
$qs['3emp'] = "SELECT * from page LIMIT 5000";
$qs['e2mp'] = "SELECT * from page LIMIT 5000";
$qs['emp2'] = "SELECT * from page LIMIT 5000";
$qs['emp8'] = "SELECT * from page LIMIT 5000";
$qs['em8p'] = "SELECT * from page LIMIT 5000";

$Connection = new Connection\MySQL('mediawiki','root','','127.0.0.1','3308');

// $conn->insert("INSERT INTO foobar VALUES ('asdddd', 'asd')");

$cbs = [
	'emp' => function ($data) {
		echo "woot" . PHP_EOL;
	}
];

$Web = new Web($Connection);
// $Web->storage($Memcached);
$Web->memory(100);
$Web->trace(__DIR__.'/out.trace');
$Web->queries($qs);
$Web->crawl(function($data){
	echo "doing work..." . PHP_EOL;
	$data = ['hello'=>'kitties'];
	return $data;
});

echo count($Web->data) . " total entries" . PHP_EOL;

foreach ($Web->data as $key => $data) {
	echo $key . " has " . count($data) . " pieces" . PHP_EOL;
}

var_dump($Web->data['emp5']);

return;

var_dump($Web->result);


$Web->crawl(function($key, $data) {
	// as queries complete process the data	
});

$Web->crawl([
	'city' => function() {

	},
	'state' => function() {

	}
]);

$d = [
	'call' => function() { echo "poo"; }
];

$d['call']();

$client->send($request)->then(function ($response) {
    echo 'Got a response! ' . $response;
});