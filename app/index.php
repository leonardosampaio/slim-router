<?php

require_once __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client('mongodb://user:password@db:27017');
$db = $client->default;
$collection = $db->messages;
$collection->insertOne(array('name' => 'test'));
$records = $collection->find( [ 'name' => 'test'] );  
foreach ($records as $record)
{  
    var_dump($record);
}