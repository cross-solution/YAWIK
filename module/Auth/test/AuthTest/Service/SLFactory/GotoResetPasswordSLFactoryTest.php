<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Service\SLFactory;

use Auth\Service\SLFactory\GotoResetPasswordSLFactory;
use Test\Bootstrap;

class GotoResetPasswordSLFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GotoResetPasswordSLFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new GotoResetPasswordSLFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $sm->setService('repositories', $repositoriesMock);

        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Auth\Service\GotoResetPassword', $result);
    }
}