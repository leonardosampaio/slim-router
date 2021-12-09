<?php

namespace api;

use util\CurlWrapper;

class ContractConsumer
{
    private $url;

    public function __construct($config)
    {
        $this->url = $config->contractServer->url;
    }

    public function sendMessage($message)
    {
        return (new CurlWrapper())->post($this->url, $message);
    }
}