<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Mail;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Core\Mail\HTMLTemplateMessage;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationName;
use Organizations\Entity\OrganizationReference;
use Organizations\Mail\EmployeeInvitationFactory;
use Zend\Router\RouteStackInterface;

/**
 * Tests for \Organizations\Mail\EmployeeInvitationFactory
 *
 * @covers \Organizations\Mail\EmployeeInvitationFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @group Organizations
 * @group Organizations.Mail
 */
class EmployeeInvitationFactoryTest extends TestCase
{

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface and \Zend\ServiceManager\MutableCreationOptionsInterface
     */
    public function testImplementsInterfaces()
    {
        $target = new EmployeeInvitationFactory();

        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', $target);
    }

    /**
     * @testdox Allows setting of creation options for each invokation.
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage An user interface is required
     */
    public function testSetCreationOptionsThrowsExceptionIfUserIsMissing()
    {
        $options = array();
        $target = new EmployeeInvitationFactory();

        $target->setCreationOptions($options);
    }

    public function provideCreationOptionsTestData()
    {
        $user = $this->getMockForAbstractClass('\Auth\Entity\UserInterface');

        $makeArray = function ($options) use ($user) {
            $options['user'] = $user;
            return array($options, array_merge(array('user' => $user, 'token' => false, 'template' => 'organizations/mail/invite-employee'), $options));
        };

        return array(
            $makeArray(array()),
            $makeArray(array('token' => 'testToken')),
            $makeArray(array('template' => 'testTemplate')),
            $makeArray(array('token' => 'tokenTest', 'template' => 'templateTest')),
            array(array('user' => $user, 'token' => true), array('user' => $user, 'token' => false, 'template' => 'organizations/mail/invite-employee')),
            array(array('user' => $user, 'template' => new \stdClass()), array('user' => $user, 'token' => false, 'template' => 'organizations/mail/invite-employee')),
        );
    }

    /**
     * @testdox Allows setting of creation options for each invokation.
     * @dataProvider provideCreationOptionsTestData
     */
    public function testSetCreationOptions($options, $expected)
    {
        $target = new EmployeeInvitationFactory();

        $target->setCreationOptions($options);

        $this->assertAttributeEquals($expected, 'options', $target);
    }

    /**
     * @testdox Creates a proper configured HTMLTemplate Mail.
     */
    public function testInvokation()
    {
        $user = new User();
        $user->setId('testUser');
        $user->setEmail('test@user');


        $options = array(
            'token' => 'testToken',
            'user' => $user,
            'template' => 'testTemplate',
        );

        $ownerOrg = new Organization();
        $ownerOrg->setId('ownerOrg');
        $ownerOrgName = new OrganizationName('TestOwnerOrg');
        $ownerOrg->setOrganizationName($ownerOrgName);

        $userOrg = new Organization();
        $userOrg->setId('userOrg');
        $userOrgName = new OrganizationName('TestUserOrg');
        $userOrg->setOrganizationName($userOrgName);


        $orgRep = $this->getMockBuilder('\Organizations\Repository\Organization')->disableOriginalConstructor()->getMock();
        $orgRep->expects($this->exactly(2))
               ->method('findByUser')
               ->withConsecutive(array('testOwner'), array('testUser'))
               ->will($this->onConsecutiveCalls($ownerOrg, $userOrg));


        $ownerOrgRef = new OrganizationReference('testOwner', $orgRep);
        $userOrgRef = new OrganizationReference('testUser', $orgRep);

        $user->setOrganization($userOrgRef);

        $owner = new User();
        $owner->getInfo()->setFirstName('Test')->setLastName('Owner');
        $owner->setOrganization($ownerOrgRef);

        $authService = $this->getMockBuilder('\Auth\AuthenticationService')->disableOriginalConstructor()->getMock();
        $authService->expects($this->once())->method('getUser')->willReturn($owner);

        $router = $this->getMockForAbstractClass(RouteStackInterface::class);
        $router->expects($this->once())
               ->method('assemble')
               ->with(
                   array('action' => 'accept'),
                   array('name' => 'lang/organizations/invite',
                            'query' => array('token' => $options['token'], 'organization' => $ownerOrg->getId()))
               )
               ->willReturn('testUrl');

        $mailService = $this->getMockBuilder('\Core\Mail\MailService')->disableOriginalConstructor()->getMock();

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();

        $services->expects($this->exactly(3))
                 ->method('get')
                 ->withConsecutive(
                     array('AuthenticationService'),
                     array('Router'),
                     ['Core/MailService']
                 )->will($this->onConsecutiveCalls($authService, $router, $mailService));

        $mailMock = new HTMLTemplateMessage(new \Zend\ServiceManager\ServiceManager());
        $translator = $this->getMockBuilder('\Zend\I18n\Translator\Translator')->disableOriginalConstructor()->getMock();
        $translator
            ->expects($this->any())
            ->method('translate')
            ->will($this->returnArgument(0));
        $mailMock->setTranslator($translator);
        $mailService
            ->expects($this->once())
            ->method('get')
            ->with('htmltemplate')
            ->willReturn($mailMock);


        $target = new EmployeeInvitationFactory();
        $mail = $target->__invoke($services, 'irrelevant', $options);


        $vars = $mail->getVariables()->getArrayCopy();

        $expected = array(
            'inviter' => 'Test Owner',
            'organization' => 'TestOwnerOrg',
            'token' => $options['token'],
            'user' => '',
            'hasAssociation' => true,
            'url' => 'testUrl',
            'isOwner' => true,
            'currentOrganization' => 'TestUserOrg',
            'template' => 'testTemplate',
        );

        $this->assertEquals($expected, $vars);
        $this->assertEquals($user->getEmail(), $mail->getTo()->current()->getEmail());
    }
}
