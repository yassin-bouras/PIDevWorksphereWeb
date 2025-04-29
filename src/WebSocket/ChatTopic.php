<?php

namespace App\WebSocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\WebSocket\WsConnection;
use React\EventLoop\LoopInterface;

class ChatTopic implements MessageComponentInterface
{
    protected $clients;
    protected $herculesSocket;
    protected $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->clients = new \SplObjectStorage;
        $this->loop = $loop;
        $this->connectToHercules();
    }

    protected function connectToHercules()
    {
        try {
            $context = stream_context_create();
            $this->herculesSocket = stream_socket_client('tcp://127.0.0.1:8888', $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
            if ($this->herculesSocket) {
                stream_set_blocking($this->herculesSocket, false);
                echo "Connected to Hercules TCP server\n";

                // Set up periodic check for Hercules messages
                $this->loop->addPeriodicTimer(0.1, function () {
                    $this->checkHerculesMessages();
                });
            }
        } catch (\Exception $e) {
            echo "Could not connect to Hercules: {$e->getMessage()}\n";
        }
    }

    protected function checkHerculesMessages()
    {
        if (!$this->herculesSocket) return;

        $data = fgets($this->herculesSocket);
        if ($data !== false) {
            $msg = trim($data);
            if (!empty($msg)) {
                echo "Received from Hercules: {$msg}\n";
                // Broadcast to all WebSocket clients
                foreach ($this->clients as $client) {
                    $client->send($msg);
                }
            }
        }
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Forward message to Hercules
        if ($this->herculesSocket) {
            $written = fwrite($this->herculesSocket, $msg . "\n");
            if ($written === false) {
                echo "Failed to write to Hercules\n";
            } else {
                echo "Sent to Hercules: {$msg}\n";
            }
            fflush($this->herculesSocket);
        }

        // Forward message to other WebSocket clients
        foreach ($this->clients as $client) {
            if ($client !== $from) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    public function __destruct()
    {
        if ($this->herculesSocket) {
            fclose($this->herculesSocket);
        }
    }
}
