<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new \MongoDB\Client('mongodb://user:password@db:27017');
$db = $client->default;
$collection = $db->messages;
$inserted = $collection->insertOne(array('name' => 'test', 'createdAt' => new MongoDB\BSON\UTCDateTime(time())));

// var_dump($inserted);die();

$records = $collection->find( [ 'name' => 'test'] );  
foreach ($records as $record)
{  
    var_dump($record);
}

var_dump($collection->deleteOne(['_id' => $inserted->getInsertedId()]));