<?php

require_once __DIR__ . '/vendor/autoload.php';

$config = file_get_contents(__DIR__ . '/../configuration.json');
$r = new redis\Redis(json_decode($config));
$r->test();