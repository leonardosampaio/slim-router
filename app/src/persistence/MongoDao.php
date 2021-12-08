<?php

namespace persistence;

class MongoDao
{
    private $db;
    private $unsentState;
    private $sentState;

    public function __construct($config)
    {
        $connection = new \MongoDB\Client(
            "mongodb://$config->db->user:$config->db->password@$config->db->host:$config->db->port");
        $this->db = $connection->{$config->database};

        $this->unsentState = $config->unsentState;
        $this->sentState = $config->sentState;

        $this->messageLimit = $config->messageLimit;
        $this->timeLimit = $config->timeLimit;

        $this->collection = $config->db->collection;
    }

    public function saveMessage($messageArray)
    {
        $time = time();

        $messageArray->State = $this->unsentState;
        $messageArray->updatedAt = new \MongoDB\BSON\UTCDateTime($time);

        $messagesCollection = $this->db->messages;
        $messagesCollection->createIndex(['State' => 1]);
        $messagesCollection->createIndex(['updatedAt' => 1]);
        $document = $messagesCollection->insertOne($messageArray);

        $result = new \stdClass();
        $result->document = $document;
        $result->updatedAt = $time;

        return $result;
    }

    public function setMessageSent($id)
    {
        return $this->db->messages->updateOne(
            ['_id' => $id],
            ['$set' => ['State' => $this->sentState]]
        );
    }
}