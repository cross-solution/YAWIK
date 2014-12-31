<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Service;

use Auth\Entity\User;
use Auth\Service\ForgotPassword;
use Auth\Service\Register;
use AuthTest\Entity\Provider\UserEntityProvider;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class RegisterTest extends \PHPUnit_Framework_TestCase
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
    public function setUp()
    {
        $this->userRepositoryMock = $this->getMockBuilder('Auth\Repository\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->testedObject = new Register($this->userRepositoryMock);

        $this->inputFilterMock = $this->getMock('Zend\InputFilter\InputFilterInterface');
        $this->mailerPluginMock = $this->getMock('Core\Controller\Plugin\Mailer');
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

    public function testProceed_WhenUserAlreadyExists()
    {
        $name = uniqid('name');
        $email = uniqid('email') . '@' . uniqid('host') . '.com.pl';
        $user = UserEntityProvider::createEntityWithRandomData();

        $this->inputFilterMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->inputFilterMock->expects($this->once())
            ->method('get')
            ->with('register')
            ->willReturnSelf();

        $this->inputFilterMock->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnOnConsecutiveCalls($name, $email);

        $this->userRepositoryMock->expects($this->once())
            ->method('findByLoginOrEmail')
            ->with($email)
            ->willReturn($user);

        $this->setExpectedException('Auth\Service\Exception\UserAlreadyExistsException', 'User already exists');

        $this->testedObject->proceed($this->inputFilterMock, $this->mailerPluginMock, $this->urlPluginMock);
    }

    public function testProceed()
    {
        $name = uniqid('name') . ' ' . uniqid('surname');
        $email = uniqid('email') . '@' . uniqid('host') . '.com.pl';
        $user = UserEntityProvider::createEntityWithRandomData();
        $confirmationLink = uniqid('confirmationLink');

        $this->inputFilterMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->inputFilterMock->expects($this->once())
            ->method('get')
            ->with('register')
            ->willReturnSelf();

        $this->inputFilterMock->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnOnConsecutiveCalls($name, $email);

        $this->userRepositoryMock->expects($this->once())
            ->method('findByLoginOrEmail')
            ->with($email)
            ->willReturn(null);

        $user->setLogin($email)->setRole(User::ROLE_RECRUITER);

        $this->userRepositoryMock->expects($this->once())
            ->method('create')
            ->with(array('login' => $email, 'role' => User::ROLE_RECRUITER))
            ->willReturn($user);

        $this->userRepositoryMock->expects($this->once())
            ->method('store')
            ->with($this->callback(function ($user) {
                $this->assertInstanceOf('Auth\Entity\User', $user);

                return $user;
            }));

        $this->urlPluginMock->expects($this->once())
            ->method('fromRoute')
            ->with(
                'lang/register-confirmation',
                array('userId' => $user->getId()),
                array('force_canonical' => true)
            )->willReturn($confirmationLink);

        $this->mailerPluginMock->expects($this->once())
            ->method('__invoke')
            ->with(
                'Auth\Mail\RegisterConfirmation',
                array(
                    'user' => $user,
                    'confirmationLink' => $confirmationLink
                ),
                true
            );

        $this->testedObject->proceed($this->inputFilterMock, $this->mailerPluginMock, $this->urlPluginMock);
    }

}