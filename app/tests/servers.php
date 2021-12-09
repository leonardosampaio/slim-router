<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use persistence\MongoDao;
use queue\RedisConsumer;

$app = AppFactory::create();

//show errors
$app->addErrorMiddleware(true, true, true);

$app->get('/', function($request, $response)
{
    return $response->withJson(['request'=>json_encode($_REQUEST)], 200);
});

$app->post('/state-change', function($request, $response)
{
    return $response->withJson(['request'=>json_decode(file_get_contents('php://input'))], 200);
});

$app->post('/contract', function($request, $response)
{
    return $response->withJson(['request'=>json_decode(file_get_contents('php://input'))], 200);
});

$app->run();