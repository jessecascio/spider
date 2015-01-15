<?php

namespace Spider\Nest;

/**
 * Command line script 
 *
 * @package Nest
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */

// grab the options
$opts = getopt("q:m:");

// output, max memory used, time taken, query fired
while (true) {
	var_dump($opts);
	var_dump(base64_decode($opts['q']));
	sleep(60);
}