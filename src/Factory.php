<?php

namespace Clue\React\SolusVM\Api;

use React\EventLoop\LoopInterface;
use Clue\React\Buzz\Browser;

class Factory
{
    private $loop;
    private $browser;

    public function __construct(LoopInterface $loop, Browser $browser = null)
    {
        if ($browser === null) {
            $browser = new Browser($loop);
        }

        $this->loop = $loop;
        $this->browser = $browser;
    }

    public function createClient($address)
    {
        if (is_array($address)) {
            $parts = $address;
        } else {
            // prepend dummy scheme to improve parsing for legacy PHP versions
            if (strpos($address, '://') === false) {
                $address = 'dummy://' . $address;
            }

            $parts = parse_url($address);

            if ($parts === false || !isset($parts['scheme'], $parts['host'])) {
                throw new \InvalidArgumentException('Given API URL can not be parsed');
            }

            if ($parts['scheme'] === 'dummy') {
                unset($parts['scheme']);
            }
        }

        if (!isset($parts['user'], $parts['user'])) {
            throw new \InvalidArgumentException('Given API URL must include user (API hash) and pass (API key)');
        }

        if (!isset($parts['port']) && !isset($parts['scheme'])) {
            $parts['port'] = 5656;
        }

        if (!isset($parts['host'])) {
            $parts['host'] = 'localhost';
        }

        if (!isset($parts['scheme'])) {
            $parts['scheme'] = 'https';
        }

        if (!isset($parts['path'])) {
            $parts['path'] = '/api/client/command.php';
        }

        $str = $parts['scheme'] . '://' . $parts['host'];
        if (isset($parts['port'])) {
            $str .= ':' . $parts['port'];
        }
        $str .= $parts['path'];

        return new Client($str, $parts['pass'], $parts['user'], $this->browser);
    }
}
