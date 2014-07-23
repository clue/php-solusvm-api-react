<?php

require __DIR__ . '/../vendor/autoload.php';

use Clue\Http\React\Factory;
use Clue\React\Buzz\Browser;
use Clue\React\SolusVM\Api\Client;

$loop = React\EventLoop\Factory::create();

$factory = new \Clue\React\SolusVM\Api\Factory($loop);
$client = $factory->createClient('https://147492ddec07231c2de7e5865880fd0191955916:Y4WNA-TZS6J-15YMB@manage.crissic.net/api/client/command.php');

//$client = new Client('https://manage.crissic.net/api/client/command.php', 'Y4WNA-TZS6J-15YMB', '147492ddec07231c2de7e5865880fd0191955916', $browser);

function e($ex) {
    echo $ex->getMessage() . PHP_EOL;
}

$client->info()->then(function ($result) {
    var_dump('INFO', $result);
}, 'e');

$client->status()->then(function ($result) {
    var_dump('STATUS', $result);
}, 'e');

$loop->run();
