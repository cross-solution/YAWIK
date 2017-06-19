<?php

namespace JobsTest\Factory;

use Zend\ServiceManager\ServiceManager;
use Jobs\Factory\ModuleOptionsFactory;
use Jobs\Options\ModuleOptions;

/**
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ModuleOptionsFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test, if configuration overwrites default values
     *
     * @dataProvider providerTestFactory
     * @covers \Jobs\Factory\ModuleOptionsFactory
     */
    public function testFactory($config)
    {
        $serviceManager = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                               ->disableOriginalConstructor()->getMock();



        if (isset($config['core_options'])) {
            $coreOptions = new \Core\Options\ModuleOptions($config['core_options']);
            $serviceManager->expects($this->exactly(2))
                           ->method('get')
                           ->withConsecutive(array('Config'), array('Core/Options'))
                           ->will($this->onConsecutiveCalls($config, $coreOptions));
        } else {
            $serviceManager->expects($this->once())->method('get')->with('Config')->willReturn($config);
        }

        $factory = new ModuleOptionsFactory;
        $defaultOption = new ModuleOptions(array());

        $object = $factory->__invoke($serviceManager,'irrelevant');

        $this->assertInstanceOf('Jobs\Options\ModuleOptions', $object);

        if (isset($config['jobs_options'])) {
            $this->assertNotEquals($defaultOption->getMultipostingApprovalMail(), $object->getMultipostingApprovalMail());
            $this->assertEquals($config['jobs_options']['multipostingApprovalMail'], $object->getMultipostingApprovalMail());
        } else {
            $this->assertEquals($config['core_options']['system_message_email'], $object->getMultipostingApprovalMail());
        }
    }

    public function providerTestFactory()
    {
        return array(
            array(
                array('core_options' => array(
                        'system_message_email' => 'default@example.com'

                ))), // if no multipostingApprovalMail is set, the core system message email must be used.
            array(
                array('jobs_options'=>array(
                    'multipostingApprovalMail' => 'test@test.de',
                )))
        );
    }
}
