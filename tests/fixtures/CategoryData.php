<?php

namespace Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Api\Sms\Entity\Category;

class CategoryData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('default');
        $category->setUser($this->getReference('user1'));
        $manager->persist($category);

        $category2 = new Category();
        $category2->setName('Category 2');
        $category2->setUser($this->getReference('user1'));
        $manager->persist($category2);

        $category3 = new Category();
        $category3->setName('default');
        $category3->setUser($this->getReference('user2'));
        $manager->persist($category3);

        $manager->flush();

        $this->addReference('user1_category1', $category);
        $this->addReference('user1_category2', $category2);
        $this->addReference('user2_category1', $category3);
    }

    public function getDependencies()
    {
        return ['Fixture\UserData'];
    }
}