<?php

namespace Api\Test\Sms\Entity;

use Api\Sms\Entity\Host;
use Api\Sms\Entity\User;

class HostTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateObject()
    {
        $this->assertInstanceOf('Api\Sms\Entity\Host', new Host());
    }

    public function testClassHasAllAttributes()
    {
        $reflection = new \ReflectionClass('Api\Sms\Entity\Host');
        $properties = $reflection->getProperties();
        $this->assertCount(4, $properties);

        foreach ($properties as $property) {
            $this->assertContains($property->getName(), ['id', 'ip', 'name', 'user']);
        }
    }

    public function testSetAndGetValues()
    {
        $object = new Host();
        $object->setIp('127.0.0.1');
        $object->setName('localhost');
        $object->setUser(new User());

        $this->assertEquals('localhost', $object->getName());
        $this->assertEquals('127.0.0.1', $object->getIp());
        $this->assertInstanceOf('Api\Sms\Entity\User', $object->getUser());
    }

    public function testSetNotAUser()
    {
        try {
            $object = new Host();
            $object->setUser(new \stdClass());
        } catch (\Exception $e) {
            return;
        }

        $this->fail('An fatal error has not been raised.');
    }
}
 