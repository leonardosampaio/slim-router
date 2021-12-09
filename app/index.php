<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use persistence\MongoDao;
use queue\RedisConsumer;

$app = AppFactory::create();

//show errors
$app->addErrorMiddleware(true, true, true);

// 1. Communicate bi-directionally with the API server.
//Receiving POST requests from the API server
$app->post('/receive', function($request, $response)
{
    $rawPayload = file_get_contents('php://input');

    if (empty($rawPayload) || empty($objPayload = json_decode($rawPayload)))
    {
        return $response->whithJson(['error'=>'Invalid payload'])->withStatus(400);
    }

    $config =
        json_decode(file_get_contents(__DIR__ . '/../configuration.json'));

    // 2. Format and store every message received from the API server to the database 
    $document = (new MongoDao($config))->saveMessage($objPayload);

    if ($document->getInsertedId() != null)
    {
        if ((new RedisConsumer($config))->saveMessage($document))
        {
            return $response->withJson(['messageSaved' => $document], 200);
        }
        
        return $response->withJson(['error' => 'Error saving message to queue'], 400);

    }

    return $response->withJson(['error' => 'Error saving message to database'], 400);
});

$app->run();