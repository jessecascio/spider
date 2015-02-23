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

ini_set("memory_limit", intval($opts['m']) . "M");

// grab the connection params
$conn = json_decode(base64_decode($opts['c']),true);
$pdo  = new Connection\MySQL($conn['db'],$conn['usr'],$conn['pwd'],$conn['hst'],$conn['prt']);

// grab the query
$query  = base64_decode($opts['q']);
$result = $pdo->query($query);

// grab the storage info
$s = json_decode(base64_decode($opts['s']),true);
$Storage = new $s['class']();
$Storage->wake($s['params']);
$Storage->table($opts['o']);

// save
$Storage->store(base64_decode($opts['k']), $result);
