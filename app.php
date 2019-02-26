<?php

use App\Event\Push;
use App\Service\Auth;
use GuzzleHttp\Client;
use React\Http\Server;
use React\Http\Response;
use App\Event\Installation;
use App\Service\CommandFactory;

require 'vendor/autoload.php';

/* Create event loop. */
$loop = React\EventLoop\Factory::create();

/* Create needed services. */
$httpClient = new Client();
$authService = new Auth($httpClient);
$commandFactory = new CommandFactory($loop, __DIR__ . '/resources');

/* Create server instance */
$server = new Server([
    new Installation($authService, $commandFactory),
    new Push($authService, $commandFactory),
    function (\Psr\Http\Message\ServerRequestInterface $request): Response {
        return new Response(200);
    },
]);

/* Open socket*/
$socket = new React\Socket\Server(8080, $loop);
$server->listen($socket);

/* Log error */
$server->on('error', function (Exception $exception) {
    echo 'Error: ' . $exception->getMessage() . PHP_EOL;
    echo 'File: ' . $exception->getFile() . PHP_EOL;
    echo 'Line: ' . $exception->getLine() . PHP_EOL;
});

/* Start app */
$loop->run();
