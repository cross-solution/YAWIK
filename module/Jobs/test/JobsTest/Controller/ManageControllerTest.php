<?php

namespace JobsTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ManageControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
             include '/config/application.config.php'
        );
        parent::setUp();
    }

    public function testActionCanBeAccessed()
    {
        $this->dispatch('/test');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Jobs');
        $this->assertControllerName('Jobs\Controller\Manage');
        $this->assertControllerClass('ManageController');
        //$this->assertMatchedRouteName('album');
    }
}