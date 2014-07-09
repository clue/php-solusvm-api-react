<?php

namespace Clue\React\SolusVM\Api;

use Clue\React\Buzz\Browser;
use Clue\React\Buzz\Message\Response;

class Client
{
    private $url;
    private $key;
    private $hash;
    private $browser;

    public function __construct($url, $key, $hash, Browser $browser)
    {
        $this->url = $url;
        $this->key = $key;
        $this->hash = $hash;
        $this->browser = $browser;
    }

    public function reboot()
    {
        return $this->request('reboot');
    }

    public function boot()
    {
        return $this->request('boot');
    }

    public function shutdown()
    {
        return $this->request('shutdown');
    }

    public function status()
    {
        return $this->request('status');
    }

    public function info($ipaddr = true, $hdd = true, $mem = true, $bw = true)
    {
        $args = array();

        if ($ipaddr) {
            $args['ipaddr'] = 'true';
        }
        if ($hdd) {
            $args['hdd'] = 'true';
        }
        if ($mem) {
            $args['mem'] = 'true';
        }
        if ($bw) {
            $args['bw'] = 'true';
        }

        return $this->request('info', $args)->then(function ($data) {

            $e = function ($d) {
                return explode(',', $d);
            };

            $u = function ($d){
                return array(
                    'total' => (float)$d[0],
                    'used'  => (float)$d[1],
                    'free'  => (float)$d[2],
                    'percentused' => (float)$d[3]
                );
            };

            if (isset($data['ipaddr'])) {
                $data['ipaddr'] = $e($data['ipaddr']);
            }

            foreach (array('hdd', 'mem', 'bw') as $key) {
                if (isset($data[$key])) {
                    $data[$key] = $u($e($data[$key]));
                }
            }

            return $data;
        });
    }

    private function request($action, array $args = array())
    {
        $url = $this->url . '?' . http_build_query(array(
            'key' => $this->key,
            'hash' => $this->hash,
            'action' => $action
        ) + $args);

        return $this->browser->get($url)->then(
            function (Response $response) {
                preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', (string)$response->getBody(), $match);
                $result = array();
                foreach ($match[1] as $x => $y) {
                    $result[$y] = $match[2][$x];
                }

                if ($result['status'] === 'error') {
                    throw new \RuntimeException('Received error message "' . $result['statusmsg'] . '" from API', 0, new \RuntimeException($result['statusmsg']));
                }

                return $result;
            },
            function (\Exception $error) {
                throw new \RuntimeException('Transport error "' . $error->getMessage() . '" while trying to access API', 0, $error);
            }
        );
    }
}
