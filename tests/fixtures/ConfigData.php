<?php

namespace Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Api\Sms\Entity\Config;

class ConfigData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $config = new Config();
        $config->setCategory($this->getReference('user1_category1'));
        $config->setGateway($this->getReference('conectta_gateway'));
        $manager->persist($config);

        $config2 = new Config();
        $config2->setCategory($this->getReference('user1_category2'));
        $config2->setGateway($this->getReference('zenvia_gateway'));
        $manager->persist($config2);

        $config3 = new Config();
        $config3->setCategory($this->getReference('user2_category1'));
        $config3->setGateway($this->getReference('zombie_gateway'));
        $manager->persist($config3);

        $manager->flush();

        $this->addReference('user1_config1', $config);
        $this->addReference('user1_config2', $config2);
        $this->addReference('user2_config1', $config3);
    }

    public function getDependencies()
    {
        return ['Fixture\CategoryData', 'Fixture\GatewayData'];
    }
}