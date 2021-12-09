<?php

require __DIR__ . '/vendor/autoload.php';

use Slim\Factory\AppFactory;
use persistence\MongoDao;
use queue\RedisConsumer;

$app = AppFactory::create();

//show errors
$app->addErrorMiddleware(true, true, true);

$config =
        json_decode(file_get_contents(__DIR__ . '/configuration.json'));

/**
 * Main entrypoint, will receive POST requests from the API server.
 * 
 * Sample request:
 * 
 * curl --request POST --data '{"message":"content"}' --header 'Content-Type: application/json' http://localhost:81/receive
 * 
 * "1. Communicate bi-directionally with the API server."
 */
$app->post('/receive', function($request, $response) use ($config)
{
    $rawPayload = file_get_contents('php://input');

    if (empty($rawPayload) ||
        empty($payloadJsonArray = json_decode($rawPayload)) ||
        json_last_error() !== JSON_ERROR_NONE ||
        !in_array($payloadJsonArray->operation,
            ['create_wallet', 'create_nft_series', 'transfer_nft'])) 
    {
        return $response->withJson(['error'=>'Invalid payload'])->withStatus(400);
    }

    // 2. Format and store every message received from the API server to the database

    $insertedId = (new MongoDao($config))->saveMessage($payloadJsonArray);

    if ($insertedId)
    {
        if ((new RedisConsumer($config))->saveMessage($payloadJsonArray))
        {
            $responseArray = [
                'success' => true
            ];
        
            switch ($payloadJsonArray->operation)
            {
                case 'create_wallet': 
                {
                    $responseArray['operation'] = 'create_wallet_out';
                    $responseArray['args'] = $payloadJsonArray->args;
                    break;
                }
                case 'create_nft_series':
                {
                    $responseArray['operation'] = 'create_nft_series_out';
                    $responseArray['args'] = [
                        'token_id' => $payloadJsonArray->args->token_id
                    ];
                    break;
                }
                case 'transfer_nft':
                {
                    $responseArray['operation'] = 'transfer_nft_out';
                }
                default:
                {
                    break;
                }
            }

            return $response->withJson($responseArray, 200);
        }
        
        return $response->withJson(['error' => 'Error saving message to queue'], 400);

    }

    return $response->withJson(['error' => 'Error saving message to database'], 400);
});

/**
 * Example of how to retrive messages
 */
$app->get('/', function($request, $response) use ($config)
{
    $number = 50;
    $dao = new MongoDao($config);
    $messages = $dao->latestMessages($config->sentState, $number);
    return $response->withJson($messages)->withStatus(200);
});

$app->run();