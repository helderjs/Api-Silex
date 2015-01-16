<?php

namespace Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Api\Sms\Entity\Gateway;

class GatewayData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $conectta = new Gateway();
        $conectta->setName('Conectta SMS');
        $conectta->setService('Conectta');

        $zenvia = new Gateway();
        $zenvia->setName('Zenvia SMS');
        $zenvia->setService('Zenvia');

        $zombie = new Gateway();
        $zombie->setName('Zombie SMS');
        $zombie->setService('Zombie');

        $manager->persist($conectta);
        $manager->persist($zenvia);
        $manager->persist($zombie);
        $manager->flush();

        $this->addReference('conectta_gateway', $conectta);
        $this->addReference('zenvia_gateway', $zenvia);
        $this->addReference('zombie_gateway', $zombie);
    }
}