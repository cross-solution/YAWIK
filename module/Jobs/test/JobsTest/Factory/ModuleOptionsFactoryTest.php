<?php

namespace JobsTest\Factory;

use Zend\ServiceManager\ServiceManager;
use Jobs\Factory\ModuleOptionsFactory;
use Jobs\Options\ModuleOptions;

class ModuleOptionsFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test, if configuration overwrites default values
     *
     * @dataProvider providerTestFactory
     */
    public function testFactory($config)
    {
        $serviceManager = new ServiceManager;
        $serviceManager->setService('Config', $config);

        $factory = new ModuleOptionsFactory;
        $defaultOption = new ModuleOptions(array());

        $object = $factory->createService($serviceManager);

        $this->assertInstanceOf('Jobs\Options\ModuleOptions', $object);

        if (isset($config['jobs_options'])) {
            $this->assertNotEquals($defaultOption->getMultipostingApprovalMail(), $object->getMultipostingApprovalMail());
            $this->assertEquals($config['jobs_options']['multipostingApprovalMail'], $object->getMultipostingApprovalMail());
        } else {
            $this->assertEquals($defaultOption->getMultipostingApprovalMail(), $object->getMultipostingApprovalMail());
        }
    }

    public function providerTestFactory()
    {
        return array(
            array(array()), // config without applications
            array(array('jobs_options'=>array(
                'multipostingApprovalMail' => 'test@test.de',
            )))
        );
    }
}

