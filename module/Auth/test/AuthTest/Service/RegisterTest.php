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

use Auth\Entity\User;
use Auth\Service\ForgotPassword;
use Auth\Service\Register;
use Core\Options\ModuleOptions;
use AuthTest\Entity\Provider\UserEntityProvider;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class RegisterTest extends TestCase
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
    private $mailServiceMock;

    /**
     * @var MockObject
     */
    private $mailMock;

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
     *
     */
    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->getMockBuilder('Auth\Repository\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mailServiceMock = $this->getMockBuilder('Core\Mail\MailService')
                                         ->disableOriginalConstructor()
                                         ->getMock();

        $this->mailMock = $this->getMockBuilder('\Core\Mail\HTMLTemplateMessage')
            ->disableOriginalConstructor()
            ->getMock();


        $this->optionsMock = $this->getMockBuilder('Auth\Options\ModuleOptions')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->optionsMock = new ModuleOptions();



        $this->testedObject = new Register($this->userRepositoryMock, $this->mailServiceMock, $this->optionsMock);

        $this->inputFilterMock = $this->getMockBuilder('Zend\InputFilter\InputFilterInterface')->getMock();
        $this->mailerPluginMock = $this->getMockBuilder('Core\Controller\Plugin\Mailer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlPluginMock = $this->getMockBuilder('Zend\Mvc\Controller\Plugin\Url')->getMock();
    }

    public function testProceed_WhenInputFilterIsInvalid()
    {
        $this->inputFilterMock->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $this->expectException('LogicException');
        $this->expectExceptionMessage('Form is not valid');

        $this->testedObject->proceed($this->inputFilterMock, $this->mailerPluginMock, $this->urlPluginMock);
    }

    /**
     * if the user already exists, there should be no user returned,
     * therefore the program has nothing it can owrk on,
     * the existing entity remains untouched
     */
    public function testProceed_WhenUserAlreadyExists()
    {
        $name = uniqid('name');
        $email = uniqid('email') . '@' . uniqid('host') . '.com.pl';
        $user = UserEntityProvider::createEntityWithRandomData();
        $role = 'user';

        $this->inputFilterMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->inputFilterMock->expects($this->once())
            ->method('get')
            ->with('register')
            ->willReturnSelf();

        $this->inputFilterMock->expects($this->exactly(3))
            ->method('getValue')
            ->willReturnOnConsecutiveCalls($name, $email, $role);

        // this does the trick with providing an already existing user
        $this->userRepositoryMock->expects($this->once())
            ->method('findByLoginOrEmail')
            ->with($email)
            ->willReturn($user);

        $this->expectException('Auth\Service\Exception\UserAlreadyExistsException');
        $this->expectExceptionMessage('User already exists');
        $this->testedObject->proceed($this->inputFilterMock, $this->mailerPluginMock, $this->urlPluginMock);

        //$this->assertEmpty($this->testedObject->proceed($this->inputFilterMock, $this->mailerPluginMock, $this->urlPluginMock));
    }

    public function proceedDataProvider()
    {
        return [
            ['user'],
            ['recruiter'],
        ];
    }

    /**
     * @dataProvider proceedDataProvider
     *
     * @param $role
     */
    public function testProceed($role)
    {
        $name = uniqid('name') . ' ' . uniqid('surname');
        $email = uniqid('email') . '@' . uniqid('host') . '.com.pl';
        $user = UserEntityProvider::createEntityWithRandomData();
        $confirmationLink = uniqid('confirmationLink');
        $self = $this;

        $this->inputFilterMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->inputFilterMock->expects($this->once())
            ->method('get')
            ->with('register')
            ->willReturnSelf();

        $this->inputFilterMock->expects($this->exactly(3))
            ->method('getValue')
            ->willReturnOnConsecutiveCalls($name, $email, $role);

        $this->userRepositoryMock->expects($this->once())
            ->method('findByLoginOrEmail')
            ->with($email)
            ->willReturn(null);

        $user->setLogin($email)->setRole(User::ROLE_RECRUITER);


        $this->userRepositoryMock->expects($this->once())
            ->method('create')
            ->with(array('login' => $email, 'role' => $role))
            ->willReturn($user);

        $this->userRepositoryMock->expects($this->once())
            ->method('store')
            ->with($this->callback(function ($user) {
                return $user instanceof User ? true:false;
            }));

        $this->urlPluginMock->expects($this->once())
            ->method('fromRoute')
            ->with(
                'lang/register-confirmation',
                array('userId' => $user->getId()),
                array('force_canonical' => true)
            )->willReturn($confirmationLink);

        $this->mailServiceMock->expects($this->once())
                              ->method('get')
                              ->with('htmltemplate')
                              ->willReturn($this->mailMock);

        $this->testedObject->proceed($this->inputFilterMock, $this->mailerPluginMock, $this->urlPluginMock);
    }
}
