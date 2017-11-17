<?php

namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use PDO;

class Chat implements MessageComponentInterface
{
    protected $clients;


    public function deleteOne($id)
    {
        echo('deleteOne');
        $db = new PDO('mysql:host=localhost;dbname=errands', 'root', '²');
        $req = $db->exec('DELETE FROM errands WHERE id=' . $id);
        var_dump($req);
    }

    public function createOne($name)
    {
        var_dump('php create :' . $name);
        $db = new PDO('mysql:host=localhost;dbname=errands', 'root', '²');
        $req = $db->prepare('INSERT INTO errands (name) VALUES(:name)');
        $req->execute(array(':name' => $name));
    }


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
            $numRecv = count($this->clients) - 1;
            echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
                , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    // The sender is not the receiver, send to each client connected
                    $client->send($msg);
                }
            }
            self::deleteOne($msg);


        } else {
            self::createOne($msg);
            foreach ($this->clients as $client) {

                    $client->send('refresh');

            }
        }
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