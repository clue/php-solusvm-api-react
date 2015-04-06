# clue/solusvm-api-react [![Build Status](https://travis-ci.org/clue/php-solusvm-api-react.svg?branch=master)](https://travis-ci.org/clue/php-solusvm-api-react)

Simple async access to your VPS box through the SolusVM API, built on top of [React PHP](http://reactphp.org/)

Solus Virtual Manager ([SolusVM](http://solusvm.com/)) is a popular commercial 
control panel (CP) for virtual private servers (VPS). Its web interface can
be used to control your VPS, see its details or boot, reboot or shutdown.

Using SolusVM is pretty common for smaller VPS hosting companies, in particular
for those listed on [Low End Box](http://lowendbox.com/). As a user (customer)
you get full root access to a VPS (usually via SSH) and use the SolusVM web
interface to manage your VPS.   

This project uses the [SolusVM Client API](http://docs.solusvm.com/client_api)
so that you can manage your VPS programatically without having to log in to the
web interface to check your bandwidth usage or reboot your VPS.

> Note: This project is in beta stage! Feel free to report any issues you encounter.

## Quickstart example

Once [installed](#install), you can use the following code to fetch the info
for your VPS from your SolusVM provider:

```php
$loop = React\EventLoop\Factory::create();
$factory = new Factory($loop);
$client = $factory->createClient(array(
    'user' => '147492ddec07231c2de7e5865880fd0191955916',
    'pass' => 'Y4WNA-TZS6J-15YMB',
    'host' => 'manage.myhost.local'
));

$client->info()->then(function ($result) {
    var_dump($result);
});

$loop->run();
```

See also the [examples](examples).

## Usage

### Factory

The `Factory` class is responsible for constructing the [`Client`](#client) instance.
It also registers everything with the main [`EventLoop`](https://github.com/reactphp/event-loop#usage).

```php
$loop = React\EventLoop\Factory::create();
$factory = new Factory($loop);
```

If you need custom DNS or proxy settings, you can explicitly pass a
custom [`Browser`](https://github.com/clue/php-buzz-react#browser) instance:

```php
$browser = new Clue\React\Buzz\Browser($loop);
$factory = new Factory($loop, $browser);
```

#### createClient()

The `createClient($address)` method can be used to create a new [`Client`](#client) instance.
You have to pass an address as either of the following:
* string `{scheme}://{hash/user}:{key/pass}@{host}/{path}`, for example `https://147492ddec07231c2de7e5865880fd0191955916:Y4WNA-TZS6J-15YMB@manage.myhost.local`
* array containing the keys `scheme`, `user` (hash), `pass` (key), `host`, `port`, `path`, as per the above [quickstart example](#quickstart-example)

Only `user` and `pass` have to be set explicitly, the `Factory` assumes defaults from the URL `https://localhost:5656/api/client/command.php` for the other parts.

### Client

The `Client` class is responsible for communication with the remote SolusVM API.

#### Actions

All public methods resemble their respective SolusVM Client API actions.

```php
$client->reboot();
$client->boot();
$client->shutdown();

$client->status();
$client->info($ipaddr = true, $hdd = true, $mem = true, $bw = true);
```

Listing all available actions is out of scope here, please refer to the [class outline](src/Client.php).

#### Processing

Issuing actions is async (non-blocking), so you can actually send multiple action requests in parallel.
The SolusVM API service will respond to each request with a response value. The order is not guaranteed.
Sending requests uses a [Promise](https://github.com/reactphp/promise)-based interface that makes it easy to react to when a request is *fulfilled*
(i.e. either successfully resolved or rejected with an error):

```php
$client->status()->then(
    function ($result) {
        // response received for status action
    },
    function (Exception $e) {
        // an error occured while executing the status action
    }
});
```

## Install

The recommended way to install this library is [through composer](http://getcomposer.org).
[New to composer?](http://getcomposer.org/doc/00-intro.md)

```JSON
{
    "require": {
        "clue/solusvm-api-react": "~0.1.0"
    }
}
```

## License

MIT
