<?php
/**
 * Created by PhpStorm.
 * User: helder
 * Date: 01/12/14
 * Time: 15:17
 */

namespace Api\Test\Sms\Provider;

use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Api\Sms\Entity\Sms;
use Silex\WebTestCase;

class SmsProviderTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../../../src/app.php';
        require __DIR__ . '/../../../../../config/database.php';
        require __DIR__ . '/../../../../../config/prod.php';

        return $app;
    }

    public function setUp()
    {
        parent::setUp();

        $connection = $this->app['orm.em']->getConnection();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $dir = $this->app['app.root'] . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "fixtures";
        $loader = new Loader();
        $loader->loadFromDirectory($dir);
        $fixtures = $loader->getFixtures();

        $purger = new ORMPurger($this->app['orm.em']);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor = new ORMExecutor($this->app['orm.em'], $purger);
        $executor->execute($fixtures);
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function testInitializer()
    {
        $this->app['user'] = $this->app['orm.em']->getRepository('Api\Sms\Entity\User')
            ->findOneBy(['access_key' => '4072041796f364e7e1fdea3ce9fb835dbe41b559']);

        $category = $this->app['orm.em']->getRepository('Api\Sms\Entity\Category')
            ->findOneBy(['name' => 'default', 'user' => $this->app['user']]);

        $config = $this->app['orm.em']->getRepository('Api\Sms\Entity\Config')
            ->findOneBy(['category' => $category, 'user' => $this->app['user']]);

        $this->app['sms.initializer']();
        $this->assertEquals($this->app['user'], $this->app['sms.defaults.user']);
        $this->assertEquals($category, $this->app['sms.defaults.category']);
        $this->assertEquals($config, $this->app['sms.defaults.config']);
        $this->assertEquals($config->getGateway()->getService(), $this->app['sms.default']);
    }

    public function testGateways()
    {
        $gateways = $this->app['orm.em']->getRepository('Api\Sms\Entity\Gateway')->findAll();

        foreach ($gateways as $gateway) {
            $this->assertEquals($gateway, $this->app['sms.gateways'][$gateway->getService()]);
        }
    }

    public function testGateway()
    {
        $this->app['user'] = $this->app['orm.em']->getRepository('Api\Sms\Entity\User')
            ->findOneBy(['access_key' => '4072041796f364e7e1fdea3ce9fb835dbe41b559']);

        $category = $this->app['orm.em']->getRepository('Api\Sms\Entity\Category')
            ->findOneBy(['name' => 'default', 'user' => $this->app['user']]);

        $config = $this->app['orm.em']->getRepository('Api\Sms\Entity\Config')
            ->findOneBy(['category' => $category, 'user' => $this->app['user']]);

        $this->assertEquals($this->app['sms.gateway'], $config->getGateway());
    }

    public function testCategory()
    {
        $this->app['user'] = $this->app['orm.em']->getRepository('Api\Sms\Entity\User')
            ->findOneBy(['access_key' => '4072041796f364e7e1fdea3ce9fb835dbe41b559']);

        $category = $this->app['orm.em']->getRepository('Api\Sms\Entity\Category')
            ->findOneBy(['name' => 'default', 'user' => $this->app['user']]);

        $this->assertEquals($this->app['sms.category'], $category);
    }

    public function testSend()
    {
        $this->app['user'] = $this->app['orm.em']->getRepository('Api\Sms\Entity\User')
            ->findOneBy(['access_key' => '4072041796f364e7e1fdea3ce9fb835dbe41b559']);

        $category = $this->app['orm.em']->getRepository('Api\Sms\Entity\Category')
            ->findOneBy(['name' => 'default', 'user' => $this->app['user']]);

        $config = $this->app['orm.em']->getRepository('Api\Sms\Entity\Config')
            ->findOneBy(['category' => $category, 'user' => $this->app['user']]);

        $sms  = new Sms();
        $sms->setDdd('71');
        $sms->setNumber('91898583');
        $sms->setType(Sms::SMS_TYPE_OUT);
        $sms->setStatus(Sms::SMS_STATUS_CREATED);
        $sms->setMessage('Message generated by unit test.');
        $sms->setSignature('Unit Test Provider');
        $sms->setCategory($config->getCategory());
        $sms->setGateway($config->getGateway());
        $this->app['orm.em']->persist($sms);
        $this->app['orm.em']->flush();

        $smsSend = $this->app['sms.send']($sms);
        $this->assertInstanceOf('Api\Sms\Entity\Sms', $smsSend);
        $this->assertEquals(Sms::SMS_STATUS_SCHEDULE, $smsSend->getStatus());
    }

    public function testStatus()
    {
        $this->app['user'] = $this->app['orm.em']->getRepository('Api\Sms\Entity\User')
            ->findOneBy(['access_key' => '4072041796f364e7e1fdea3ce9fb835dbe41b559']);

        $category = $this->app['orm.em']->getRepository('Api\Sms\Entity\Category')
            ->findOneBy(['name' => 'default', 'user' => $this->app['user']]);

        $config = $this->app['orm.em']->getRepository('Api\Sms\Entity\Config')
            ->findOneBy(['category' => $category, 'user' => $this->app['user']]);

        $sms  = new Sms();
        $sms->setDdd('71');
        $sms->setNumber('91898583');
        $sms->setType(Sms::SMS_TYPE_OUT);
        $sms->setStatus(Sms::SMS_STATUS_CREATED);
        $sms->setMessage('Message generated by unit test.');
        $sms->setSignature('Unit Test Provider');
        $sms->setCategory($config->getCategory());
        $sms->setGateway($config->getGateway());
        $this->app['orm.em']->persist($sms);
        $this->app['orm.em']->flush();

        $smsSend = $this->app['sms.send']($sms);
        $this->assertInstanceOf('Api\Sms\Entity\Sms', $smsSend);
        $this->assertEquals(Sms::SMS_STATUS_SCHEDULE, $smsSend->getStatus());

        $smsStatus = $this->app['sms.status']($smsSend);
        $this->assertInstanceOf('Api\Sms\Entity\Sms', $smsStatus);
        $this->assertEquals(Sms::SMS_STATUS_SCHEDULE, $smsStatus->getStatus());
    }
}
 