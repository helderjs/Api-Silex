<?php

namespace Api\Test\Sms\Entity;

use Api\Sms\Entity\Gateway;

class GatewayTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateObject()
    {
        $this->assertInstanceOf('Api\Sms\Entity\Gateway', new Gateway());
    }

    public function testClassHasAllAttributes()
    {
        $reflection = new \ReflectionClass('Api\Sms\Entity\Gateway');
        $properties = $reflection->getProperties();
        $this->assertCount(3, $properties);

        foreach ($properties as $property) {
            $this->assertContains($property->getName(), ['id', 'name', 'service']);
        }
    }

    public function testSetAndGetValues()
    {
        $object = new Gateway();
        $object->setName('Conectta SMS');
        $object->setService('Conectta');

        $this->assertEquals('Conectta SMS', $object->getName());
        $this->assertEquals('Conectta', $object->getService());
    }
}
