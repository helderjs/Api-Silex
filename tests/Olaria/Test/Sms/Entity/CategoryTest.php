<?php

namespace Api\Test\Sms\Entity;

use Api\Sms\Entity\Category;
use Api\Sms\Entity\User;

class CategoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateObject()
    {
        $this->assertInstanceOf('Api\Sms\Entity\Category', new Category());
    }

    public function testClassHasAllAttributes()
    {
        $reflection = new \ReflectionClass('Api\Sms\Entity\Category');
        $properties = $reflection->getProperties();
        $this->assertCount(3, $properties);

        foreach ($properties as $property) {
            $this->assertContains($property->getName(), ['id', 'name', 'user']);
        }
    }

    public function testSetAndGetValues()
    {
        $object = new Category();
        $object->setName('Category 1');
        $object->setUser(new User());

        $this->assertEquals('Category 1', $object->getName());
        $this->assertInstanceOf('Api\Sms\Entity\User', $object->getUser());
    }

    public function testSetNotAUser()
    {
        try {
            $object = new Category();
            $object->setUser(new \stdClass());
        } catch (\Exception $e) {
            return;
        }

        $this->fail('An fatal error has not been raised.');
    }
}
 