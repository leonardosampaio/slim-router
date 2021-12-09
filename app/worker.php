<?php

/**
 * CLI worker that will process the Redis message queue and update the MongoDB database.
 */

use queue\RedisConsumer;
use api\ContractConsumer;
use api\ApiServerConsumer;
use persistence\MongoDao;

require __DIR__ . '/vendor/autoload.php';

if( !defined('STDIN') || !(empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0))
{
    die("This should run in CLI");
}

$config                 = json_decode(
                            file_get_contents(__DIR__ . '/configuration.json'));

$redisConsumer          = new RedisConsumer($config);
$contractApiConsumer    = new ContractConsumer($config);
$apiServerConsumer      = new ApiServerConsumer($config);
$dao                    = new MongoDao($config);

while (true)
{
    if (!empty($message = $redisConsumer->getNextMessage()))
    {
        // 4. Communicate bi-directionally with the contract server. 
        // Send the next item to execute
        $messageSentResult = $contractApiConsumer->sendMessage($message);

        //Read success messages from the contract server and store them to the database
        if ($messageSentResult->httpcode === 200 &&
            $dao->setMessageSent($message, $messageSentResult->response))
        {
            // 1. Communicate bi-directionally with the API server.
            // 7. Communicate changes in "State" on an item to the API server. 
            $response = $apiServerConsumer->sendSateChangeMessage($message);

            //TODO validate $response?
        }
    }
    else
    {
        //the queue is empty, sleep for $config->workerDelayInMilliseconds
        //waiting for new messages
        usleep($config->workerDelayInMilliseconds * 1000);
    }
}