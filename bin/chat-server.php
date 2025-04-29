<?php

use App\WebSocket\ChatTopic;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Dotenv\Dotenv;
use React\EventLoop\Factory;

require dirname(__DIR__) . '/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

$loop = Factory::create();
$chatTopic = new ChatTopic($loop);

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            $chatTopic
        )
    ),
    8888,
    '127.0.0.1',
    $loop
);

echo "WebSocket server running on ws://127.0.0.1:8888\n";
$server->run();
