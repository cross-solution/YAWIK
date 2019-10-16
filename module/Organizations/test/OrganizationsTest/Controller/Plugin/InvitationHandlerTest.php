<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Controller\Plugin;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Organizations\Controller\Plugin\InvitationHandler;
use Zend\Validator\EmailAddress;

/**
 * Tests for \Organizations\Controller\Plugin\InvitationHandler
 *
 * @covers \Organizations\Controller\Plugin\InvitationHandler
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Controller
 * @group Organizations.Controller.Plugin
 */
class InvitationHandlerTest extends TestCase
{
    private $target;
    private $emailValidatorMock;
    private $translatorMock;
    private $userRepositoryMock;
    private $userTokenGeneratorMock;
    private $mailerPluginMock;

    protected function setUp(): void
    {
        $name = $this->getName(false);
        $this->target = new InvitationHandler();
    }

    private function setupMocks($mocks = array(), $inject = false)
    {
        if (is_bool($mocks) || (is_array($mocks) && empty($mocks))) {
            $inject = $mocks;
            $mocks = array('emailValidator', 'translator', 'userRepository', 'userTokenGenerator', 'mailerPlugin');
        }

        if (!is_array($mocks)) {
            $mocks = array($mocks);
        }

        if (in_array('emailValidator', $mocks)) {
            $this->emailValidatorMock = new EmailAddress();
        }
        if (in_array('translator', $mocks)) {
            $this->translatorMock = $this->getMockBuilder('\Zend\I18n\Translator\Translator')->getMock();
        }
        if (in_array('userRepository', $mocks)) {
            $this->userRepositoryMock = $this->getMockBuilder('\Auth\Repository\User')->disableOriginalConstructor()->getMock();
        }
        if (in_array('userTokenGenerator', $mocks)) {
            $this->userTokenGeneratorMock = $this->getMockBuilder('\Auth\Service\UserUniqueTokenGenerator')
                                             ->disableOriginalConstructor()->getMock();
        }
        if (in_array('mailerPlugin', $mocks)) {
            $this->mailerPluginMock   = $this->getMockBuilder('\Core\Controller\Plugin\Mailer')
                                         ->disableOriginalConstructor()->getMock();
        }

        if ($inject) {
            foreach ($mocks as $key) {
                $setter = 'set' . $key;
                $this->target->$setter($this->{$key . 'Mock'});
            }
        }
    }


    /**
     * @testdox Extends \Zend\Mvc\Controller\Plugin\AbstractPlugin
     */
    public function testExtendsAbstractPlugin()
    {
        $this->assertInstanceOf('\Zend\Mvc\Controller\Plugin\AbstractPlugin', $this->target);
    }

    public function provideSetterAndGetterTestData()
    {
        return array(
            array('emailValidator'),
            array('translator'),
            array('userRepository'),
            array('userTokenGenerator'),
            array('mailerPlugin')
        );
    }

    /**
     * Allows setting and getting dependencies.
     * @dataProvider provideSetterAndGetterTestData()
     *
     * @param $mockKey
     */
    public function testSetterAndGetter($mockKey)
    {
        $this->setupMocks($mockKey);

        $setter = 'set' . ucfirst($mockKey);
        $getter = 'get' . ucfirst($mockKey);
        $value  = $this->{$mockKey . 'Mock'};

        $this->assertSame($this->target, $this->target->$setter($value), 'Fluent interface broken!');
        $this->assertSame($value, $this->target->$getter());
    }

    /**
     * @testdox Throws exception if dependencies are missing.
     * @dataProvider provideSetterAndGetterTestData()
     *
     * @expectedException \Core\Exception\MissingDependencyException
     */
    public function testGetterThrowExceptionIfDependencyMissing($getterName)
    {
        $getter = "get" . $getterName;

        $this->target->$getter();
    }

    public function testReturnsErrorResultIfEmailAddressIsInvalidOrEmpty()
    {
        $this->setupMocks(array('emailValidator', 'translator'), /* inject */ true);
        $email = 'invalidEmailAddress';
        $message = 'Email address is invalid.';
        $this->translatorMock->expects($this->exactly(5))
                             ->method('translate')
                             ->with($message)
                             ->will($this->returnArgument(0));

        $expected = array(
            'ok' => false,
            'message' => $message,
        );

        foreach (array($email, null, '', 0, array()) as $testEmail) {
            $result = $this->target->process($testEmail);

            $this->assertEquals($expected, $result);
        }
    }

    public function testFindsExistentUsers()
    {
        $email = 'test@yawik.org';
        $this->setupMocks(true);

        $user = new User();
        $user->setId('testUserId');
        $user->setEmail($email);
        $user->getInfo()->setEmail($email);

        $this->userRepositoryMock->expects($this->once())->method('findByEmail')->with($email, null)
                                 ->willReturn($user);
        $this->userRepositoryMock->expects($this->never())->method('create');
        $this->userTokenGeneratorMock->expects($this->once())->method('generate')->with($user, 7)->willReturn('testToken');

        $this->mailerPluginMock->expects($this->once())->method('__invoke');

        $expected = array(
            'ok' => true,
            'result' => array(
                'userId' => $user->getId(),
                'userName' => $user->getInfo()->getDisplayName(),
                'userEmail' => $email
            )
        );

        $result = $this->target->process($email);

        $this->assertEquals($expected, $result);
    }

    public function testCreatesNewUser()
    {
        $this->setupMocks(true);

        $email = 'test@yawik.org';

        $user = new User();
        $user->setId('testUserId');
        $user->setEmail($email);
        $user->getInfo()->setEmail($email);

        $this->userRepositoryMock->expects($this->once())->method('findByEmail')->with($email, null)
                                 ->willReturn(null);
        $this->userRepositoryMock->expects($this->once())->method('create')->willReturn($user);
        $this->userTokenGeneratorMock->expects($this->once())->method('generate')->with($user, 7)->willReturn('testToken');

        $this->mailerPluginMock->expects($this->once())->method('__invoke');

        $expected = array(
            'ok' => true,
            'result' => array(
                'userId' => $user->getId(),
                'userName' => $user->getInfo()->getDisplayName(),
                'userEmail' => $email
            )
        );

        $result = $this->target->process($email);

        $this->assertEquals($result, $expected);
        $this->assertTrue($user->isDraft(), 'User is not in draft mode!');
    }

    public function testReturnsErrorResultIfMailSendingFailed()
    {
        $email = 'test@yawik.org';
        $this->setupMocks(true);

        $user = new User();
        $user->setId('testUserId');
        $user->setEmail($email);
        $user->getInfo()->setEmail($email);

        $message = 'Sending invitation mail failed.';
        $this->translatorMock->expects($this->once())
                             ->method('translate')
                             ->with($message)
                             ->will($this->returnArgument(0));

        $this->userRepositoryMock->expects($this->once())->method('findByEmail')->with($email, null)
                                 ->willReturn($user);
        $this->userRepositoryMock->expects($this->never())->method('create');
        $this->userTokenGeneratorMock->expects($this->once())->method('generate')->with($user, 7)->willReturn('testToken');

        $this->mailerPluginMock->expects($this->once())->method('__invoke')->will($this->throwException(new \Exception()));

        $expected = array(
            'ok' => false,
            'message' => $message
        );

        $result = $this->target->process($email);

        $this->assertEquals($expected, $result);
    }
}
