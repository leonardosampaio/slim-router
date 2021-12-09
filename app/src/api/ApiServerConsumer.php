<?php

namespace api;

use util\CurlWrapper;

class ApiServerConsumer
{
    private $stateChangeUrl;

    public function __construct($config)
    {
        $this->stateChangeUrl = $config->contractServer->stateChangeUrl;
    }

    public function sendSateChangeMessage($message)
    {
        return (new CurlWrapper())->post($this->stateChangeUrl, $message);
    }
}