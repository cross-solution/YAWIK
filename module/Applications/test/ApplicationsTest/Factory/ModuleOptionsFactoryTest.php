<?php

namespace ApplicationsTest\Factory;

use PHPUnit\Framework\TestCase;

use Zend\ServiceManager\ServiceManager;
use Applications\Factory\ModuleOptionsFactory;
use Applications\Options\ModuleOptions;

class ModuleOptionsFactoryTest extends TestCase
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

        $this->assertInstanceOf('Applications\Options\ModuleOptions', $object);

        if (isset($config['application_options'])) {
            $this->assertNotEquals($defaultOption->getAttachmentsMaxSize(), $object->getAttachmentsMaxSize());
            $this->assertEquals($config['application_options']['attachmentsMaxSize'], $object->getAttachmentsMaxSize());
        } else {
            $this->assertEquals($defaultOption->getAttachmentsMaxSize(), $object->getAttachmentsMaxSize());
        }
    }

    public function providerTestFactory()
    {
        return array(
            array(array()), // config without applications
            array(array('application_options'=>array(
                'attachmentsMaxSize' => 2000000,
            )))
        );
    }
}
