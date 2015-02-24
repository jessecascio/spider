<?php

use Spider\Component\Web;
use Spider\Component\Config;
use Spider\Connection;
use Spider\Storage;
use Spider\Component\Logger;

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

/*
$log = Logger::instance('trace.out');
$log->addError('booty', ['asdasdsad']);

return;
*/

$qs = []; // SLEEP(FLOOR(0 + (RAND() * 2)))
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs['harry'] = "select * from page limit 1";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs[] = "SELECT SLEEP(FLOOR(0 + (RAND() * 2)))";
$qs['ford'] = "select * from page limit 2";

/*
$dsn = 'mysql:host=127.0.0.1;port=3308;dbname=mediawiki;';

$pdo = new PDO($dsn, 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$start = microtime(true);

foreach ($qs as $q) {
	$stmt = $pdo->query($q);
	$stmt->fetchAll();
//	sleep(mt_rand(2,4));
}

echo "TOOK: " . number_format(microtime(true)-$start,4);
echo PHP_EOL;

die();
*/

$start = microtime(true);

$Connection = new Connection\MySQL('mediawiki','root','','127.0.0.1','3308');
$Memcached  = new Storage\Memcached('127.0.0.1', 11211);

$Config = new Config();
$Config->parse('parameters.ini');
$Config->storage($Memcached);

$Web = new Web($Connection, $Config);
$Web->queries($qs);
$Web->crawl(function($data) {return $data;});

$took = number_format(microtime(true)-$start,4);

$count = 0;

foreach ($Web->results() as $set) {
	$count += count($set);
}

$r = count($Web->results());

// var_dump($Web->result);

echo "$r RESULTS" . PHP_EOL;
echo "$count TOTAL RESULTS" . PHP_EOL;
echo "TOOK: " . $took;
echo PHP_EOL;

die();

/*
$Web->crawl(function($data){
	echo "doing work..." . PHP_EOL;
	$data = ['hello'=>'kitties'];
	return $data;
});
*/

//$Web->memory(100);
//$Web->trace(__DIR__.'/out.trace');
// $conn->insert("INSERT INTO foobar VALUES ('asdddd', 'asd')");

$cbs = [
	'emp' => function ($data) {
		echo "woot" . PHP_EOL;
	}
];

/*	
	$Config = new Config();
	$Config->parse('parameters.ini');
	$Config->storage($Memcached);

	$Web = new Web($Connection, $Config);

*/

$Config = new Config();
$Config->parse('parameters.ini');

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

echo count($Web->data()) . " total entries" . PHP_EOL;

foreach ($Web->data() as $key => $data) {
	echo $key . " has " . count($data) . " pieces" . PHP_EOL;
}

var_dump($Web->data()['emp5']);

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