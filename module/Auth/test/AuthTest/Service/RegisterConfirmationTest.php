<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Service;

use PHPUnit\Framework\TestCase;

use Auth\Service\RegisterConfirmation;
use AuthTest\Entity\Provider\UserEntityProvider;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class RegisterConfirmationTest extends TestCase
{
    /**
     * @var RegisterConfirmation
     */
    private $testedObject;

    /**
     * @var MockObject
     */
    private $userRepositoryMock;

    /**
     * @var MockObject
     */
    private $authenticationServiceMock;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->getMockBuilder('Auth\Repository\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->authenticationServiceMock = $this->getMockBuilder('Zend\Authentication\AuthenticationService')
            ->setMethods(array('getStorage', 'write'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->testedObject = new RegisterConfirmation($this->userRepositoryMock, $this->authenticationServiceMock);
    }

    public function testProceed_WhenUserCannotFound()
    {
        $user = UserEntityProvider::createEntityWithRandomData();
        $userId = $user->getId();

        $this->userRepositoryMock->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn(null);

        $this->expectException('Auth\Service\Exception\UserNotFoundException');

        $this->testedObject->proceed($userId);
    }

    public function testProceed()
    {
        $user = UserEntityProvider::createEntityWithRandomData();
        $userId = $user->getId();

        $this->userRepositoryMock->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $this->authenticationServiceMock->expects($this->once())
            ->method('getStorage')
            ->willReturnSelf();

        $this->authenticationServiceMock->expects($this->once())
            ->method('write')
            ->with($user->getId());

        $this->testedObject->proceed($userId);
    }
}
