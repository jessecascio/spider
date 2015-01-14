<?php

/*
$m = new Memcached();
$m->addServer('127.0.0.1', 11211);
$m->set('poo', 'happy');
*/
require __DIR__ . "/src/Spider/Spider.php";

$qs = [];
$qs['city'] = "SELECT * FROM city";
$qs['state'] = "SELECT * FROM state";
$qs['county'] = "SELECT * FROM county";
$qs['country'] = "SELECT * FROM country";

$Spider = new Spider();
$Spider->weeve($qs);
$Spider->crawl();