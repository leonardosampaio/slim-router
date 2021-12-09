<?php

namespace queue;

use Predis\Client;

class RedisConsumer {

    private $client;
    private $messageLimit;
    private $timeLimitInSeconds;
    private $countKey;
    private $messagesKey;

    public function __construct($config)
    {
        $this->client = new Client([
            'scheme' => $config->redis->scheme,
            'host'   => $config->redis->host,
            'port'   => $config->redis->port
        ]);
        $this->client->connect();

        $this->messagesKey = $config->redis->messagesKey;
        $this->countKey = $config->redis->countKey;

        // 6. Set communication thresholds -- send the next item to the contract
        // server immediately unless more than X transactions were sent in the last Y seconds.
        // (these variables should be able to change) 
        $this->messageLimit = $config->messageLimit;
        $this->timeLimitInSeconds = $config->timeLimitInSeconds;
    }

    public function deleteAll()
    {
        return $this->client->flushAll();
    }

    public function saveMessage($document)
    {
        return $this->client->rpush($this->messagesKey, json_encode($document));
    }

    public function getNextMessage()
    {
        if (!$this->client->exists($this->countKey))
        {
            $this->client->set($this->countKey, 1);
            $this->client->expire($this->countKey, $this->timeLimitInSeconds);
            
            return $this->client->lpop($this->messagesKey);
        }

        $total = $this->client->get($this->countKey);

        if ($total <= $this->messageLimit)
        {
            $this->client->incr($this->countKey);
            
            return $this->client->lpop($this->messagesKey);
        }

        return null;
    }
}