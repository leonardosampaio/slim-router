<?php

namespace api;

use util\CurlWrapper;

class ContractConsumer
{
    private $url;
    private $port;
    private $timeoutInSeconds;

    public function __construct($config)
    {
        $this->url = $config->contractServer->url;
        $this->port = isset($config->contractServer->port) ?
            $config->contractServer->port : 443;
        $this->timeoutInSeconds = isset($config->contractServer->timeoutInSeconds) ?
            $config->contractServer->timeoutInSeconds : 10;
    }

    /**
     * Send a message to the Contract server on $this->url
     * 
     * @param string $message raw json to be sent
     * 
     * @return object httpcode and raw json response from the Contract server
     */
    public function sendMessage($message)
    {
        return (new CurlWrapper())->post($this->url,
        $message,
        [],
        $this->timeoutInSeconds,
        $this->port);
    }
}