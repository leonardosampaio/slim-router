<?php

namespace persistence;

use \MongoDB\Client;
class MongoDao
{
    private $connection;
    private $db;
    private $unsentState;
    private $sentState;

    public function __construct($config)
    {
        $this->connection = new Client(
            "mongodb://".
            $config->db->user.
            ":".
            $config->db->password.
            "@".
            $config->db->host.
            ":".
            $config->db->port
        );
        $this->db = $this->connection->{$config->db->database};

        $this->unsentState = $config->unsentState;
        $this->sentState = $config->sentState;

        $this->messageLimit = $config->messageLimit;
        $this->timeLimit = $config->timeLimit;

        $this->collection = $config->db->collection;
    }

    public function saveMessage($messageArray)
    {
        $time = time();

        // 3. Store each item with an integer field "State" 
        $messageArray->State = $this->unsentState;
        $messageArray->updatedAt = new \MongoDB\BSON\UTCDateTime($time);

        $messagesCollection = $this->db->messages;
        //5. Index the database to quickly query the earliest items that were added to the database based on the items "State" 
        $messagesCollection->createIndex(['State' => 1]);
        $messagesCollection->createIndex(['updatedAt' => 1]);

        return $messagesCollection->insertOne($messageArray);
    }

    public function setMessageSent($document, $contractApiResponse)
    {
        $session = $this->connection->startSession();
        $session->startTransaction();
        try {
            
            $updated = $this->db->messages->updateOne(
                ['_id' => $document->getInsertedId()],
                ['$set' => ['State' => $this->sentState]]
            );

            $inserted = $this->db->contract_responses->insertOne(
                json_decode($contractApiResponse)
            );

            return $updated && $inserted;

            $session->commitTransaction();
        } catch(\Exception $e) {
            $session->abortTransaction();
        }

        return false;
    }
}