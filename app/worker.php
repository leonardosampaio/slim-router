<?php

use queue\RedisConsumer;
use api\ContractConsumer;
use api\ApiServerConsumer;
use persistence\MongoDao;

require __DIR__ . '/vendor/autoload.php';

if( !defined('STDIN') || !(empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0))
{
    die("This should run in CLI");
}

$config = json_decode(file_get_contents(__DIR__ . '/configuration.json'));

$redisConsumer = new RedisConsumer($config);
$contractApiConsumer = new ContractConsumer($config);
$apiServerConsumer = new ApiServerConsumer($config);
$dao = new MongoDao($config);

while (true)
{
    if (!empty($message = $redisConsumer->getNextMessage()))
    {
        echo 'msg: ' . $message . PHP_EOL;

        // 4. Communicate bi-directionally with the contract server. 
        // Send the next item to execute
        $response = $contractApiConsumer->sendMessage($message);

        //Read success messages from the contract server and store them to the database
        if ($result->httpcode === 200 &&
            $dao->setMessageSent($message, $response))
        {
            // 1. Communicate bi-directionally with the API server.
            // 7. Communicate changes in "State" on an item to the API server. 
            $apiServerConsumer->sendSateChangeMessage($message);
        }
    }
    else
    {
        //empty queue, sleep for $config->workerDelay miliseconds
        usleep($config->workerDelay * 1000);
    }
}