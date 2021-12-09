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

        //queue ids
        $this->messagesKey = $config->redis->messagesKey;
        $this->countKey = $config->redis->countKey;

        // 6. Set communication thresholds -- send the next item to the contract
        // server immediately unless more than X transactions were sent in the last Y seconds.
        // (these variables should be able to change) 
        $this->messageLimit = $config->messageLimit;
        $this->timeLimitInSeconds = $config->timeLimitInSeconds;
    }

    /**
     * For testing purposes
     */
    public function deleteAll()
    {
        return $this->client->flushAll();
    }

    /**
     * Save a new message to $this->messagesKey queue
     * 
     * @param object $message message to save
     * 
     * @return int|bool in case of success, the number of the message in the queue
     */
    public function saveMessage(&$message)
    {
        try {
            $result = $this->client->rpush($this->messagesKey, json_encode($message));

            if (is_int($result) && $result > 0)
            {
                $message->redisQueuePosition = $result;
                return $result;
            }

            return false;
        }
        catch (\Exception $e){
            return false;
        }
    }

    /**
     * Get the next avaliable message from the queue, if not rate limited
     * 
     * @return string in case of success, the message
     */
    public function getNextMessage()
    {
        //quota queue is empty, initialize it
        if (!$this->client->exists($this->countKey))
        {
            $message = $this->client->lpop($this->messagesKey);

            $this->client->set($this->countKey, !empty($message) ? 1 : 0);
            //restart quota in $this->timeLimitInSeconds
            $this->client->expire($this->countKey, $this->timeLimitInSeconds);

            return $message;
        }

        $quotaConsumed = $this->client->get($this->countKey);

        if ($quotaConsumed <= $this->messageLimit &&
            !empty($message = $this->client->lpop($this->messagesKey)))
        {
            //valid quota, increment quota consumed number
            $this->client->incr($this->countKey);
            return $message;
        }

        return null;
    }
}