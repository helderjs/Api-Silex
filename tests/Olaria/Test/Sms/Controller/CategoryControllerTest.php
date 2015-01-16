<?php

namespace Api\Test\Sms\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpKernel\Client;
use Silex\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client = null;

    public function createApplication()
    {
        $app = require __DIR__ . '/../../../../../src/app.php';
        require __DIR__ . '/../../../../../config/database.php';
        require __DIR__ . '/../../../../../config/dev.php';

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

        $this->client = $this->createClient();
    }

    public function testGetCategory()
    {
        $this->client->request('GET', "/sms/category/1?key=4072041796f364e7e1fdea3ce9fb835dbe41b559");
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(
            '{"id":1,"category":"default","config":"Conectta SMS"}',
            $this->client->getResponse()->getContent()
        );
    }

    public function testAddCategory()
    {
        $category = [
            'name' => 'Category 3',
            'gateway' => 2,
        ];
        $this->client->request('POST', "/sms/category?key=4072041796f364e7e1fdea3ce9fb835dbe41b559", $category);
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(
            '{"id":4,"category":"Category 3","config":"Zenvia SMS"}',
            $this->client->getResponse()->getContent()
        );
    }

    public function testDeleteCategoryFail()
    {
        $this->client->request('DELETE', "/sms/category/1?key=4072041796f364e7e1fdea3ce9fb835dbe41b559");
        $this->assertTrue($this->client->getResponse()->isServerError());
        $this->assertEquals(
            '{"success":false,"error":"Error while trying delete the category."}',
            $this->client->getResponse()->getContent()
        );
    }

    public function testDeleteCategorySuccess()
    {
        $this->client->request('DELETE', "/sms/category/2?key=4072041796f364e7e1fdea3ce9fb835dbe41b559");
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertEquals(
            '{"success":true}',
            $this->client->getResponse()->getContent()
        );
    }
}
