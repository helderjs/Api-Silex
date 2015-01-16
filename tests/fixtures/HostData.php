<?php

namespace Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Api\Sms\Entity\Host;

class HostData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $host = new Host();
        $host->setIp('127.0.0.1');
        $host->setName('localhost');
        $host->setUser($this->getReference('user1'));

        $manager->persist($host);
        $manager->flush();

        $this->addReference('user1_host1', $host);
    }

    public function getDependencies()
    {
        return ['Fixture\UserData'];
    }
}