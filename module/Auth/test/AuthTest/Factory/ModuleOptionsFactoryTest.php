<?php

namespace AuthTest\Factory;

use PHPUnit\Framework\TestCase;

use Zend\ServiceManager\ServiceManager;
use Auth\Factory\ModuleOptionsFactory;
use Auth\Options\ModuleOptions;

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

        $this->assertInstanceOf('Auth\Options\ModuleOptions', $object);

        if (isset($config['auth_options'])) {
            $this->assertNotEquals($defaultOption->getFromName(), $object->getFromName());
            $this->assertEquals($config['auth_options']['from_name'], $object->getFromName());
        } else {
            $this->assertNotEquals($defaultOption->getFromEmail(), '<string:email@example.com>');
            $this->assertEquals($defaultOption->getFromName(), $object->getFromName());
        }
    }

    public function providerTestFactory()
    {
        return array(
            array(array()), // config without applications
            array(array('auth_options'=>array(
                'from_name' => 'My Site Name',
                'from_email' => 'my@email.de',
            )))
        );
    }
}
