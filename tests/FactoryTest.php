<?php

use Clue\React\SolusVM\Api\Factory;

class FactoryTest extends TestCase
{
    private $factory;

    public function setUp()
    {
        $loop = React\EventLoop\Factory::create();

        $this->factory = new Factory($loop);
    }

    public function testValidAddressString()
    {
        $client = $this->factory->createClient('key:hash@localhost');

        $this->assertInstanceOf('Clue\React\SolusVM\Api\Client', $client);
    }

    public function testValidAddressArray()
    {
        $client = $this->factory->createClient(array(
            'user' => 'API hash',
            'pass' => 'API key',
        ));

        $this->assertInstanceOf('Clue\React\SolusVM\Api\Client', $client);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidUrl()
    {
        $this->factory->createClient('');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidUrlMissingAuthentication()
    {
        $this->factory->createClient('https://host:5656/without-auth');
    }
}
