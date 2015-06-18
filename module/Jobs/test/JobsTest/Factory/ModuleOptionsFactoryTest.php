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
     * @covers Jobs\Factory\ModuleOptionsFactory
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
            $this->assertEquals($config['Auth']['default_user']['email'], $object->getMultipostingApprovalMail());
        }
    }

    public function providerTestFactory()
    {
        return array(
            array(
                array('Auth' => array(
                    'default_user' => array(
                        'email' => 'default@example.com'
                    )
                ))), // if no multipostingApprovalMail is set, the default_users email should be used
            array(
                array('jobs_options'=>array(
                    'multipostingApprovalMail' => 'test@test.de',
                )))
        );
    }
}