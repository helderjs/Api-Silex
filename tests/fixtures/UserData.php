<?php

namespace Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Api\Sms\Entity\User;

class UserData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('User 1');
        $user->setAccessKey('4072041796f364e7e1fdea3ce9fb835dbe41b559');
        $manager->persist($user);

        $user2 = new User();
        $user2->setName('User 2');
        $user2->setAccessKey('7c4a8d09ca3762af61e59520943dc26494f8941b');
        $manager->persist($user2);

        $manager->flush();

        $this->addReference('user1', $user);
        $this->addReference('user2', $user2);
    }
}