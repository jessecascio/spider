<?php

use Spider\Web;
use Spider\Driver;

error_reporting(-1);

/*
$m = new Memcached();
$m->addServer('127.0.0.1', 11211);
$m->set('poo', 'happy');
*/
require __DIR__ . "/vendor/autoload.php";

$qs = [];
$qs['city'] = "SELECT * FROM city";
$qs['state'] = "SELECT * FROM state";
$qs['county'] = "SELECT * FROM county";
$qs['country'] = "SELECT * FROM country";

$Driver = new Driver\Memcached();
$Web = new Web($Driver);
$Web->queries($qs);
$Web->crawl();

var_dump($Web->result);