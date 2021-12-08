<?php

require_once __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client(

    'mongodb://user:password@localhost:27017'

);
$collection = $client->test->default;
$collection->insertOne(array('nome' => 'sdsd'));
var_dump($collection);