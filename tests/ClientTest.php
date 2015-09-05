<?php

use Clue\React\SolusVM\Api\Client;
use React\Promise\Deferred;
use Clue\React\Buzz\Message\Response;
use Clue\React\Buzz\Message\Body;
use Clue\React\Buzz\Message\Headers;

class ClientTest extends TestCase
{
    private $browser;
    private $client;

    public function setUp()
    {
        $this->browser = $this->getMockBuilder('Clue\React\Buzz\Browser')->setConstructorArgs(array($this->getMock('React\EventLoop\LoopInterface')))->setMethods(array('get'))->getMock();
        $this->browser = $this->browser->withBase('http://a/path');

        $this->client = new Client($this->browser, 'mykey', 'myhash');
    }

    public function testReboot()
    {
        $this->setupBrowser('action=reboot', '<status>success</status>
                          <statusmsg>rebooted</statusmsg>');

        $this->assertResolvesWith(
            array(
                'status' => 'success',
                'statusmsg' => 'rebooted'
            ),
            $this->client->reboot()
        );
    }

    public function testBoot()
    {
        $this->setupBrowser('action=boot', '<status>success</status>
                          <statusmsg>booted</statusmsg>');

        $this->assertResolvesWith(
            array(
                'status' => 'success',
                'statusmsg' => 'booted'
            ),
            $this->client->boot()
        );
    }

    public function testShutdown()
    {
        $this->setupBrowser('action=shutdown', '<status>success</status>
                          <statusmsg>shutdown</statusmsg>');

        $this->assertResolvesWith(
            array(
                'status' => 'success',
                'statusmsg' => 'shutdown'
            ),
            $this->client->shutdown()
        );
    }

    public function testStatus()
    {
        $this->setupBrowser('action=status', '<status>success</status>
                          <statusmsg>online</statusmsg>');

        $this->assertResolvesWith(
            array(
                'status' => 'success',
                'statusmsg' => 'online'
            ),
            $this->client->status()
        );
    }

    public function testInfo()
    {
        $this->setupBrowser('action=info&ipaddr=true&hdd=true&mem=true&bw=true', '<status>success</status>
                          <ipaddr>122.122.122.122,111.111.111.111</ipaddr>
                          <hdd>1000,800,200,80</hdd>
                          <mem>4000,1000,3000,25</mem>
                          <bw>100,0,100,0</bw>');

        $this->assertResolvesWith(
            array(
                'status' => 'success',
                'ipaddr' => array('122.122.122.122', '111.111.111.111'),
                'hdd' => array('total' => 1000, 'used' => 800, 'free' => 200, 'percentused' => 80),
                'mem' => array('total' => 4000, 'used' => 1000, 'free' => 3000, 'percentused' => 25),
                'bw' => array('total' => 100, 'used' => 0, 'free' => 100, 'percentused' => 0)
            ),
            $this->client->info()
        );
    }

    public function testApiError()
    {
        $this->setupBrowser('action=status', '<status>error</status>
                          <statusmsg>error message</statusmsg>');

        $this->expectPromiseReject($this->client->status());
    }

    public function testHttpError()
    {
        $d = new Deferred();
        $d->reject(new RuntimeException('error'));

        $this->browser->expects($this->once())
            ->method('get')
            ->with($this->equalTo('http://a/path?key=mykey&hash=myhash&action=status'), array())
            ->will($this->returnValue($d->promise()));

        $this->expectPromiseReject($this->client->status());
    }

    private function setupBrowser($expectedUrl, $fakeResponseBody)
    {
        $d = new Deferred();
        $d->resolve(new Response('HTTP/1.0', 200, 'OK', new Headers(), new Body($fakeResponseBody)));

        $this->browser->expects($this->once())
            ->method('get')
            ->with($this->equalTo('http://a/path?key=mykey&hash=myhash&' . $expectedUrl), array())
            ->will($this->returnValue($d->promise()));
    }

    private function assertResolvesWith($expected, $actual)
    {
        $this->expectPromiseResolve($actual)->then($this->expectCallableOnce($expected));
    }
}
