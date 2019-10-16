<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Service;

use PHPUnit\Framework\TestCase;

use Auth\Factory\Service\GotoResetPasswordFactory;
use CoreTest\Bootstrap;

class GotoResetPasswordFactoryTest extends TestCase
{
    /**
     * @var GotoResetPasswordFactory
     */
    private $testedObj;

    protected function setUp(): void
    {
        $this->testedObj = new GotoResetPasswordFactory();
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
