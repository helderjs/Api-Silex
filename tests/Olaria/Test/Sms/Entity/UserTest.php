<?php

namespace Api\Test\Sms\Entity;

use Api\Sms\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateObject()
    {
        $this->assertInstanceOf('Api\Sms\Entity\User', new User());
    }

    public function testClassHasAllAttributes()
    {
        $reflection = new \ReflectionClass('Api\Sms\Entity\User');
        $properties = $reflection->getProperties();
        $this->assertCount(5, $properties);

        foreach ($properties as $property) {
            $this->assertContains($property->getName(), ['id', 'name', 'access_key', 'created', 'updated']);
        }
    }

    public function testSetAndGetValues()
    {
        $object = new User();
        $object->setName('User 1');
        $object->setAccessKey('123456');

        $this->assertEquals('User 1', $object->getName());
        $this->assertEquals('123456', $object->getAccessKey());
    }
}