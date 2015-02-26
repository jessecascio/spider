<?php

namespace Spider\bin;

use Spider\Connection;
use Spider\Component\Logger;

/**
 * Command line script 
 *
 * @package Nest
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */

error_reporting(E_ALL);
require __DIR__ . "/../../../../../autoload.php";

$opts = getopt("q:m:t:k:c:s:f:");

ini_set("memory_limit", intval($opts['m']) . "M");

$Logger = Logger::instance(base64_decode($opts['f']));

// grab the connection params
try {
	$conn = json_decode(base64_decode($opts['c']),true);
	$pdo  = new Connection\MySQL($conn['db'],$conn['usr'],$conn['pwd'],$conn['hst'],$conn['prt']);
} catch (\Exception $e) {
	$Logger->addError('Error With Connection', [$e->getMessage()]);
	die();
}

// grab the query
try {
	$query  = base64_decode($opts['q']);
	$result = $pdo->query($query);
} catch (\Exception $e) {
	$Logger->addError('Error With Query', [$e->getMessage()]);
	die();
}

// grab the storage info
try {
	$s = json_decode(base64_decode($opts['s']),true);
	$Storage = new $s['class']();
	$Storage->wake($s['params']);
	$Storage->table($opts['t']);

	// save
	$Storage->store(base64_decode($opts['k']), $result);
} catch (\Exception $e) {
	$Logger->addError('Error With Storage', [$e->getMessage()]);
	die();
}

