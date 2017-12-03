<?php

namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use PDO;

class Chat implements MessageComponentInterface
{
    protected $clients;


    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (is_numeric($msg)) {

            self::deleteOne($msg);

            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    // The sender is not the receiver, send to each client connected
                    $client->send($msg);
                }
            }

        } else {
            self::createOne($msg);
            foreach ($this->clients as $client) {
                $client->send('newProduct');

            }
        }
    }

    public function deleteOne($id)
    {
        $db = new PDO('mysql:host=localhost;dbname=errands', 'root', '²');
        $db->exec('DELETE FROM errands WHERE id=' . $id);

    }

    public function createOne($name)
    {
        $db = new PDO('mysql:host=localhost;dbname=errands', 'root', '²');
        $req = $db->prepare('INSERT INTO errands (name) VALUES(:name)');
        $req->execute(array(':name' => $name));
    }


    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}