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

    /**
     * Send a message to the API server on $this->stateChangeUrl
     * 
     * @param string $message raw json to be sent
     * 
     * @return array httpcode and raw json response from the API server
     */
    public function sendSateChangeMessage($message)
    {
        return (new CurlWrapper())->post(
            $this->stateChangeUrl,
            $message,
            $this->timeoutInSeconds,
            $this->port);
    }
}