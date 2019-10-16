<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Factory\Controller;

use PHPUnit\Framework\TestCase;

use Jobs\Controller\TemplateController;
use Jobs\Factory\Controller\TemplateControllerFactory;
use Jobs\Options\ModuleOptions;
use CoreTest\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

/**
 * Class TemplateControllerFactoryTest
 * @package JobsTest\Factory\Controller
 */
class TemplateControllerFactoryTest extends TestCase
{
    /**
     * @var TemplateControllerFactory
     */
    private $testedObj;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->testedObj = new TemplateControllerFactory();
    }

    /**
     *
     */
    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $jobRepositoryMock = $this->getMockBuilder('Jobs\Repository\Job')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock->expects($this->once())
            ->method('get')
            ->with('Jobs/Job')
            ->willReturn($jobRepositoryMock);
    
        $sm->setService('repositories', $repositoriesMock);
        //@TODO: [ZF3] don't know why we can't set config in ZF3, we have to use mock
        $sm->setService('config', array('core_options' => array('system_message_email' => 'test@test.de')));
        $jobOptionsMock = $this->getMockBuilder(ModuleOptions::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $sm->setService('Jobs/Options', $jobOptionsMock);
        $result = $this->testedObj->__invoke($sm, TemplateController::class);
        $this->assertInstanceOf('Jobs\Controller\TemplateController', $result);
    }
}
