<?php

namespace Spider\Nest;

use Spider\Connection;

/**
 * Command line script 
 *
 * @package Nest
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */

require __DIR__ . "/../../../vendor/autoload.php";

// grab the options
// query
// memory
// key
// connection
// storage
$opts = getopt("q:m:o:k:c:s:");

ini_set("memory_limit", intval($opts['m']) . "M");

// run the query
$conn = json_decode(base64_decode($opts['c']),true);
$pdo  = new Connection\MySQL($conn['db'],$conn['usr'],$conn['pwd'],$conn['hst'],$conn['prt']);

$query  = base64_decode($opts['q']);
$result = $pdo->query($query);

// save in temp storage
$s = json_decode(base64_decode($opts['s']),true);
$Storage = new $s['class']();
$Storage->wake($s['params']);

$Storage->store($opts['o'], base64_decode($opts['k']), $result);

sleep(mt_rand(0,2));
