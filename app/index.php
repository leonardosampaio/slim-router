<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use persistence\MongoDao;
use redis\RedisConsumer;

$app = AppFactory::create();

//show errors
$app->addErrorMiddleware(true, true, true);

// $app->get('/get', function(Request $request, Response $response) use ()
// {

// });

$app->post('/receive', function($request, $response)
{
    $config =
        json_decode(file_get_contents(__DIR__ . '/../configuration.json'));

    $rawPayload = file_get_contents('php://input');
    $objPayload = json_decode($rawPayload);

    $result = (new MongoDao($config))->saveMessage($objPayload);

    if (isset($result->document) && $result->document->getInsertedId() != null)
    {
        if ((new RedisConsumer($config))->saveMessage($result))
        {
            return $response->withJson(['messageSaved' => $result->document], 200);
        }
        else {
            return $response->withJson(['error' => 'Error saving message to queue'], 400);
        }

    }
    else
    {
        return $response->withJson(['error' => 'Error saving message'], 400);
    }
});

$app->run();