# PHP message router

This application has two main parts, which run asynchonously:

1. Web server `app/index.php`, that receives POST requests with JSON messages, process and stores (MongoDB/Redis) them;
2. CLI script `app/worker.php`, that consumes a Redis queue to send the received messages (from the API server) to a second server (Contract server), updating the state of the sent messages on the MongoDB database.

## Instalation

1. Copy `configuration.json.dist` to `configuration.json` on root folder and edit the variables

    | Key | Description |
    | ----------- | ----------- |
    | workerDelayInMilliseconds | how many milliseconds the worker must sleep before it pools the Redis server again for new messages |
    | messageLimit | number of messages per timeLimitInSeconds the worker should consume |
    | timeLimitInSeconds | time in seconds that the worker quota of requests should be reset |
    | unsentState | initial status of a received message, that will be saved as an attribute on the database |
    | sentState | status of a processed message, that will be saved as an attribute on the database |
    | db user | MongoDB username |
    | db password | MongoDB password |
    | db host | MongoDB host name (by default, name of MongoDB service in docker-compose.yml) |
    | db database | MongoDB database |
    | db port | MongoDB port |
    | db messagesCollection | MongoDB collection that will store the messages |
    | db contractResponsesCollection | MongoDB collection that will store Contract server responses |
    | redis scheme | Redis scheme (by default, tcp) |
    | redis host | Redis scheme (by default, name of Redis service in docker-compose.yml) |
    | redis port | Redis port |
    | redis messagesKey | Redis messages queue name |
    | redis countKey | Redis quota queue name |
    | apiServer port | API server port (by default, HTTPS 443) |
    | apiServer timeoutInSeconds | API server connection and response timeout in seconds |
    | apiServer stateChangeUrl | API server URL that should be called on every message state change event |
    | apiServer apiKey | API key |
    | contractServer port | API server port (by default, HTTPS 443) |
    | contractServer timeoutInSeconds | Contract server connection and response timeout in seconds |
    | contractServer url | Contract server URL that should be called to send the next item to execute |

2. Run `docker compose up` on root folder
3. Optionally, run `docker exec -it app composer update` to update the dependencies
4. Configure API server to make POST calls to `http://localhost:81/receive`
5. Run the worker CLI script with `docker exec -it -d app /usr/local/bin/php /var/www/html/worker.php`