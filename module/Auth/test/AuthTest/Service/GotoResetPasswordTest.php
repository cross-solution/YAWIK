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

use Auth\Entity\Token;
use Auth\Service\GotoResetPassword;
use AuthTest\Entity\Provider\UserEntityProvider;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class GotoResetPasswordTest extends TestCase
{
    /**
     * @var GotoResetPassword
     */
    private $testedObject;

    /**
     * @var MockObject
     */
    private $repositoryServiceMock;

    /**
     * @var MockObject
     */
    private $authenticationServiceMock;

    /**
     * @var MockObject
     */
    private $userRepositoryMock;

    protected function setUp(): void
    {
        $this->repositoryServiceMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->authenticationServiceMock = $this->getMockBuilder('Zend\Authentication\AuthenticationService')
            ->setMethods(array('getStorage', 'write'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->userRepositoryMock = $this->getMockBuilder('Auth\Repository\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryServiceMock->expects($this->once())
            ->method('get')
            ->with('Auth/User')
            ->willReturn($this->userRepositoryMock);

        $this->testedObject = new GotoResetPassword($this->repositoryServiceMock, $this->authenticationServiceMock);
    }

    public function testProceed_WhenUserOrTokenDoesNotExists()
    {
        $user = UserEntityProvider::createEntityWithRandomData();
        $userId = $user->getId();
        $tokenHash = uniqid('tokenHash');

        $this->userRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(
                array(
                    'id' => new \MongoId($userId),
                    'tokens.hash' => $tokenHash
                )
            )
            ->willReturn(null);

        $this->expectException('Auth\Service\Exception\UserNotFoundException');

        $this->testedObject->proceed($userId, $tokenHash);
    }

    public function testProceed_WhenTokenHasExpired()
    {
        $tokenHash = uniqid('tokenHash');

        $token = new Token();
        $token->setExpirationDate(new \DateTime('2014-01-01 00:00:00'))
            ->setHash($tokenHash);

        $user = UserEntityProvider::createEntityWithRandomData();
        $user->getTokens()->add($token);

        $userId = $user->getId();

        $this->userRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(
                array(
                    'id' => new \MongoId($userId),
                    'tokens.hash' => $tokenHash
                )
            )
            ->willReturn($user);

        $this->expectException('Auth\Service\Exception\TokenExpirationDateExpiredException');

        $this->testedObject->proceed($userId, $tokenHash);
    }

    public function testProceed()
    {
        $tokenHash = uniqid('tokenHash');

        $expirationDate = new \DateTime();
        $expirationDate->modify('+1 hour');

        $token = new Token();
        $token->setExpirationDate($expirationDate)
            ->setHash($tokenHash);

        $expiredToken = new Token();
        $expiredToken->setExpirationDate(new \DateTime('2014-01-01 00:00:00'));
        $expiredToken->setHash(uniqid('tokenHash'));

        $user = UserEntityProvider::createEntityWithRandomData();
        $user->getTokens()->add($token);
        $user->getTokens()->add($expiredToken);

        $userId = $user->getId();

        $this->userRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(
                array(
                    'id' => new \MongoId($userId),
                    'tokens.hash' => $tokenHash
                )
            )
            ->willReturn($user);

        $this->repositoryServiceMock->expects($this->once())
            ->method('remove')
            ->with($expiredToken);

        $this->authenticationServiceMock->expects($this->once())
            ->method('getStorage')
            ->willReturnSelf();

        $this->authenticationServiceMock->expects($this->once())
            ->method('write')
            ->with($user->getId());

        $this->testedObject->proceed($userId, $tokenHash);
    }
}
