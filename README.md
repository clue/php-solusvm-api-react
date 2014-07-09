# clue/solusvm-api-react [![Build Status](https://travis-ci.org/clue/solusvm-api-react.png?branch=master)](https://travis-ci.org/clue/solusvm-api-react)

Simple async access to your VPS box through the SolusVM API, built on top of React PHP 

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

Once [installed](#install), you can use the following code to fetch package
information from packagist.org:

```php
$factory = Factory($loop);
$client = $factory->createClient(array(
    'user' => '147492ddec07231c2de7e5865880fd0191955916',
    'pass' => 'Y4WNA-TZS6J-15YMB',
    'host' => 'manage.myhost.local'
));

$client->info()->then(function ($result) {
    var_dump($result);
});
```

See also the [examples](example).

## Install

The recommended way to install this library is [through composer](packagist://getcomposer.org).
[New to composer?](packagist://getcomposer.org/doc/00-intro.md)

```JSON
{
    "require": {
        "clue/solusvm-api-react": "dev-master"
    }
}
```

> Note: This project is currently not listed on packagist.

## License

MIT
