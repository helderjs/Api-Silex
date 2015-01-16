<?php

namespace Api\Test\Sms\Entity;

use Api\Sms\Entity\Category;
use Api\Sms\Entity\Config;
use Api\Sms\Entity\Gateway;
use Api\Sms\Entity\User;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateObject()
    {
        $this->assertInstanceOf('Api\Sms\Entity\Config', new Config());
    }

    public function testClassHasAllAttributes()
    {
        $reflection = new \ReflectionClass('Api\Sms\Entity\Config');
        $properties = $reflection->getProperties();
        $this->assertCount(4, $properties);

        foreach ($properties as $property) {
            $this->assertContains($property->getName(), ['id', 'category', 'gateway', 'user']);
        }
    }

    public function testSetAndGetValues()
    {
        $object = new Config();
        $object->setCategory(new Category());
        $object->setGateway(new Gateway());

        $this->assertInstanceOf('Api\Sms\Entity\Category', $object->getCategory());
        $this->assertInstanceOf('Api\Sms\Entity\Gateway', $object->getGateway());
    }

    public function testSetNotACategory()
    {
        try {
            $object = new Config();
            $object->setCategory(new \stdClass());
        } catch (\Exception $e) {
            return;
        }

        $this->fail('An fatal error has not been raised.');
    }

    public function testSetNotAGateway()
    {
        try {
            $object = new Config();
            $object->setGateway(new \stdClass());
        } catch (\Exception $e) {
            return;
        }

        $this->fail('An fatal error has not been raised.');
    }
}
