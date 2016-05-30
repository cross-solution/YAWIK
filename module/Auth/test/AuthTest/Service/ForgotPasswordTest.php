<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Service;

use Auth\Service\ForgotPassword;
use AuthTest\Entity\Provider\UserEntityProvider;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class ForgotPasswordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ForgotPassword
     */
    private $testedObject;

    /**
     * @var MockObject
     */
    private $userRepositoryMock;

    /**
     * @var MockObject
     */
    private $tokenGeneratorMock;

    /**
     * @var
     */
    private $loginFilterMock;

    /**
     * @var MockObject
     */
    private $inputFilterMock;

    /**
     * @var MockObject
     */
    private $mailerPluginMock;

    /**
     * @var MockObject
     */
    private $urlPluginMock;

    /**
     * @var MockObject
     */
    private $optionsMock;

    /**
     * @var MockObject
     */
    public function setUp()
    {
        $this->userRepositoryMock = $this->getMockBuilder('Auth\Repository\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->tokenGeneratorMock = $this->getMockBuilder('Auth\Service\UserUniqueTokenGenerator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->loginFilterMock = $this->getMockBuilder('Auth\Filter\LoginFilter')
                                         ->disableOriginalConstructor()
                                         ->getMock();

        $this->optionsMock = $this->getMockBuilder('Auth\Options\ModuleOptions')
                                         ->disableOriginalConstructor()
                                         ->getMock();

        $this->testedObject = new ForgotPassword($this->userRepositoryMock, $this->tokenGeneratorMock, $this->loginFilterMock, $this->optionsMock);

        $this->inputFilterMock = $this->getMock('Zend\InputFilter\InputFilterInterface');
        $this->mailerPluginMock = $this->getMock('Core\Controller\Plugin\Mailer', [], [], '', false);
        $this->urlPluginMock = $this->getMock('Zend\Mvc\Controller\Plugin\Url');
    }

    public function testProceed_WhenInputFilterIsInvalid()
    {
        $this->inputFilterMock->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $this->setExpectedException('LogicException', 'Form is not valid');

        $this->testedObject->proceed($this->inputFilterMock, $this->mailerPluginMock, $this->urlPluginMock);
    }

    public function testProceed_WhenUserIsNotFound()
    {
        $identity = uniqid('identity');

        $this->inputFilterMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->inputFilterMock->expects($this->once())
            ->method('getValue')
            ->with('identity')
            ->willReturn($identity);

        $this->userRepositoryMock->expects($this->once())
            ->method('findByLoginOrEmail')
            ->with($identity)
            ->willReturn(null);

        $this->setExpectedException('Auth\Service\Exception\UserNotFoundException', 'User is not found');

        $this->testedObject->proceed($this->inputFilterMock, $this->mailerPluginMock, $this->urlPluginMock);
    }

    public function testProceed_WhenUserDoesNotHaveAnEmail()
    {
        $identity = uniqid('identity');
        $user = UserEntityProvider::createEntityWithRandomData(array('email' => null));

        $this->inputFilterMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->inputFilterMock->expects($this->once())
            ->method('getValue')
            ->with('identity')
            ->willReturn($identity);

        $this->userRepositoryMock->expects($this->once())
            ->method('findByLoginOrEmail')
            ->with($identity)
            ->willReturn($user);

        $this->setExpectedException('Auth\Service\Exception\UserDoesNotHaveAnEmailException', 'User does not have an email');

        $this->testedObject->proceed($this->inputFilterMock, $this->mailerPluginMock, $this->urlPluginMock);
    }

    public function testProceed()
    {
        // @TODO: fix this
        /*
        $identity = uniqid('identity');
        $user = UserEntityProvider::createEntityWithRandomData();
        $tokenHash = uniqid('tokenHash');
        $resetLink = uniqid('resetLink');

        $this->inputFilterMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->inputFilterMock->expects($this->once())
            ->method('getValue')
            ->with('identity')
            ->willReturn($identity);

        $this->userRepositoryMock->expects($this->once())
            ->method('findByLoginOrEmail')
            ->with($identity)
            ->willReturn($user);

        $this->tokenGeneratorMock->expects($this->once())
            ->method('generate')
            ->with($user)
            ->willReturn($tokenHash);

        $this->urlPluginMock->expects($this->once())
            ->method('fromRoute')
            ->with(
                'lang/goto-reset-password',
                array('token' => $tokenHash, 'userId' => $user->getId()),
                array('force_canonical' => true)
            )->willReturn($resetLink);

        $this->mailerPluginMock->expects($this->once())
            ->method('__invoke')
            ->with(
                'Auth\Mail\ForgotPassword',
                array(
                    'user' => $user,
                    'resetLink' => $resetLink
                ),
                true
            );
        */
        //$this->testedObject->proceed($this->inputFilterMock, $this->mailerPluginMock, $this->urlPluginMock);
        return true;
    }
}
