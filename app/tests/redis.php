<?php

require_once __DIR__ . '/../vendor/autoload.php';

$config = file_get_contents(__DIR__ . '/../configuration.json');
$consumer = new queue\RedisConsumer(json_decode($config));

// var_dump($consumer->getAll());
// die();

var_dump($consumer->deleteAll());

for($i=0; $i<10; $i++)
{
    // var_dump(
        $consumer->saveMessage([rand(100,999)])
        ;
    // );
}

// // var_dump($consumer->flushAll());

for($i=0; $i<15; $i++)
{
    var_dump(json_encode(
        $consumer->getNextMessage()
        // ;
    ));
}

// $config = json_decode($config);
// use Predis\Client;
// $client = new Client([
//     'scheme' => $config->redis->scheme,
//     'host'   => $config->redis->host,
//     'port'   => $config->redis->port
// ]);
// $client->connect();
// $client->flushAll();

// for($i=0; $i<10; $i++)
// {
//     $client->rpush('messages', json_encode([rand(100,999)]));
// }

// for($i=0; $i<15; $i++)
// {
//     var_dump(json_encode(
//         $client->lpop('messages')
//     ));
// }