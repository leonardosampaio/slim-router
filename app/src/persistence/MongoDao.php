<?php

namespace persistence;

use \MongoDB\Client;
class MongoDao
{
    private $connection;
    private $db;
    private $unsentState;
    private $sentState;

    private $messagesCollection;
    private $contractResponsesCollection;

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

        $this->messagesCollection = $config->db->messagesCollection;
    }

    public function saveMessage(&$messageArray)
    {
        try
        {
            $time = time();
    
            // 3. Store each item with an integer field "State" 
            $messageArray->State = $this->unsentState;
            $messageArray->updatedAt = new \MongoDB\BSON\UTCDateTime($time);
    
            $collection = $this->db->{$this->messagesCollection};
            //5. Index the database to quickly query the earliest items that were added to the database based on the items "State" 
            $collection->createIndex(['State' => 1]);
            $collection->createIndex(['updatedAt' => 1]);
    
            $inserted = $collection->insertOne($messageArray);

            if ($inserted->getInsertedCount() === 1)
            {
                $id = $inserted->getInsertedId();
    
                $messageArray->mongoDbId = $id;
                
                return $id;
            }
        }
        catch (\Exception $e){}

        return false;
    }

    public function setMessageSent($document, $contractApiResponse)
    {
        $session = $this->connection->startSession();
        $session->startTransaction();
        try {

            $updated = $this->db->{$this->messagesCollection}->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectID(
                    json_decode($document)->mongoDbId->{'$oid'})],
                ['$set' => ['State' => $this->sentState]]
            );

            $inserted = $this->db->{$this->contractResponsesCollection}->insertOne(
                json_decode($contractApiResponse)
            );

            $session->commitTransaction();

            return $updated->getModifiedCount() === 1 &&
                $inserted->getInsertedCount() === 1;

        } catch(\Exception $e) {
            $session->abortTransaction();
        }

        return false;
    }
}