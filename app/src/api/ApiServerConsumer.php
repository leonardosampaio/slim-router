<?php

namespace api;

use util\CurlWrapper;

class ApiServerConsumer
{
    private $stateChangeUrl;
    private $port;
    private $timeoutInSeconds;

    public function __construct($config)
    {
        $this->stateChangeUrl = $config->apiServer->stateChangeUrl;
        $this->port = isset($config->apiServer->port) ?
            $config->apiServer->port : 443;
        $this->timeoutInSeconds =
            isset($config->apiServer->timeoutInSeconds) ?
                $config->apiServer->timeoutInSeconds : 10;
    }

    public function sendSateChangeMessage($message)
    {
        return (new CurlWrapper())->post(
            $this->stateChangeUrl,
            $message,
            $this->timeoutInSeconds,
            $this->port);
    }
}