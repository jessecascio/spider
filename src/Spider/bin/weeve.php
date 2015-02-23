<?php

namespace Spider\bin;

use Spider\Connection;

/**
 * Command line script 
 *
 * @package Nest
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */

require __DIR__ . "/../../../vendor/autoload.php";

$opts = getopt("q:m:o:k:c:s:");

error_reporting(E_ALL);
ini_set("memory_limit", intval($opts['m']) . "M");

// grab the connection params
try {
	$conn = json_decode(base64_decode($opts['c']),true);
	$pdo  = new Connection\MySQL($conn['db'],$conn['usr'],$conn['pwd'],$conn['hst'],$conn['prt']);
} catch (\Exception $e) {
	echo "Error With Connection: " . $e->getMessage() . PHP_EOL;
	die();
}

// grab the query
try {
	$query  = base64_decode($opts['q']);
	$result = $pdo->query($query);
} catch (\Exception $e) {
	echo "Error With Query: " . $e->getMessage() . PHP_EOL;
	die();
}

// grab the storage info
try {
	$s = json_decode(base64_decode($opts['s']),true);
	$Storage = new $s['class']();
	$Storage->wake($s['params']);
	$Storage->table($opts['o']);

	// save
	$Storage->store(base64_decode($opts['k']), $result);
} catch (\Exception $e) {
	echo "Error With Storage: " . $e->getMessage() . PHP_EOL;
	die();
}

