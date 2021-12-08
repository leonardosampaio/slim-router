<?php

namespace redis;

class RedisConsumer {

    private $client;
    private $messageLimit;
    private $timeLimit;

    public function __construct($config)
    {
        $this->client = new \Predis\Client([
            'scheme' => $config->redis->scheme,
            'host'   => $config->redis->host,
            'port'   => $config->redis->port
        ]);
        $this->messageLimit = $config->messageLimit;
        $this->timeLimit = $config->timeLimit;
    }

    public function saveMessage($dbInsertResult)
    {
        //fila 1, mensagens a processar

        $id = $dbInsertResult->document->getInsertedId();
        $date = $dbInsertResult->updatedAt;
        $document = $dbInsertResult->document;

        //fila 2, timestamp de processamento com expiração

    }

    public function getNextMessage()
    {
        // if $this->messageLimit >
        // qtd de itens da fila 2 com item.time > (now - $this->timeLimit);
        //retorna uma 
        //se nao null
    }

    public function test()
    {
        $cachedData = $this->client->get($this->cacheEntryIdentifier);
        var_dump($cachedData);
        
        // if ($cachedData != null) {
            
        // }

        // $this->client->set($this->cacheEntryIdentifier, base64_encode($json));
        // $this->client->expire($this->cacheEntryIdentifier, 10);
    }

    // public function canSendMessage()
    // {
    //     return $this->messageLimit > $this->db->messages->count(
    //         ['State' => $this->sentState,
    //         'updatedAt' => ['$gte' => new \MongoDB\BSON\UTCDateTime(time() - $this->timeLimit)]]);
    // }
}