<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Controller;

use Organizations\Controller\InviteEmployeeController;
use Organizations\Controller\Plugin\AcceptInvitationHandler;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationName;
use Zend\View\Model\ViewModel;
use Organizations\Repository\Organization as OrganizationRepository;

/**
 * Tests for \Organizations\Controller\InviteEmployeeController
 * 
 * @covers \Organizations\Controller\InviteEmployeeController
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Controller
 */
class InviteEmployeeControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InviteEmployeeController
     */
    private $target;

    /**
     * @var OrganizationRepository
     */
    private $orgRepo;

    /**
     * @var array
     */
    private $queryParams;

    /**
     * @var array
     */
    private $pluginsMockMap;

    public function setup()
    {
        $this->orgRepo = $this->createMock(OrganizationRepository::class);
        $this->target = new InviteEmployeeController($this->orgRepo);

        $name = $this->getName(false);
        if ('testExtendsAbstractActionController' == $name) {
            return;
        }


        $plugins = $this
	        ->getMockBuilder('\Zend\Mvc\Controller\PluginManager')
	        ->disableOriginalConstructor()
	        ->getMock()
        ;
        $plugins
	        ->expects($this->any())
	        ->method('get')
	        ->will($this->returnCallback(array($this, 'retrievePluginMock')))
        ;

        $this->target->setPluginManager($plugins);

        $params = $this->getMockBuilder('\Zend\Mvc\Controller\Plugin\Params')
                       ->disableOriginalConstructor()
                       ->getMock()
        ;
        $params
	        ->expects($this->any())
	        ->method('__invoke')
	        ->will($this->returnSelf())
        ;
        $params
	        ->expects($this->any())
	        ->method('fromQuery')
	        ->will($this->returnCallback(array($this, 'retrieveQueryParam')))
        ;

        $this->pluginsMockMap['params'] = $params;
    }

    public function retrievePluginMock($name)
    {
        return isset($this->pluginsMockMap[$name]) ? $this->pluginsMockMap[$name] : null;
    }

    public function retrieveQueryParam($name, $default=null)
    {
        return isset($this->queryParams[$name]) ? $this->queryParams[$name] : $default;
    }

    /**
     * @testdox Extends \Zend\Mvc\Controller\AbstractActionController
     */
    public function testExtendsAbstractActionController()
    {
        $this->assertInstanceOf('\Zend\Mvc\Controller\AbstractActionController', $this->target);
    }

    public function testInviteActionReturnsJsonModel()
    {
        $email = 'test@email';
        $this->queryParams['email'] = $email;
        $result = array('var1' => 'ok', 'var2' => 'ok');

        $handler = $this->getMockBuilder('\Organizations\Controller\Plugin\InvitationHandler')
                        ->disableOriginalConstructor()->getMock();
        $handler->expects($this->once())->method('process')->with($email)->willReturn($result);

        $this->pluginsMockMap['Organizations/InvitationHandler'] = $handler;

        $model = $this->target->inviteAction();

        $this->assertInstanceOf('\Zend\View\Model\JsonModel', $model);
        $this->assertEquals($result, $model->getVariables());
    }

    private function setupOrganizationEntityRetrievalMocks($name)
    {
        $id = $name. "Id";
        $organization = new Organization();
        $organization->setOrganizationName(new OrganizationName($name));

        $this->queryParams['organization'] = $id;

        $this->orgRepo->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($organization)
        ;
    }

    private function setupForwardPluginMock($result)
    {
        $model = new ViewModel($result);

        $forward = $this->getMockBuilder('\Zend\Mvc\Controller\Plugin\Forward')->disableOriginalConstructor()->getMock();
        $forward->expects($this->once())->method('dispatch')->with('Auth\Controller\Password', array('action' => 'index'))
                ->willReturn($model);

        $this->pluginsMockMap['forward'] = $forward;
    }

    public function provideAcceptActionOnPostRequestTestData()
    {
        return array(
            array(array('valid' => false, 'form' => 'testForm'), array('organization' => 'testOrg', 'form' => 'testForm')),
            array(array('valid' => true, 'form' => 'testFormShouldNotBeHere'), array('organization' => 'test2Org'))
        );
    }

    /**
     * @dataProvider provideAcceptActionOnPostRequestTestData
     *
     * @param $result
     * @param $expected
     */
    public function testAcceptActionReturnsSetPasswordViewModelOnPostRequests($result, $expected)
    {
        $request = $this->target->getRequest();
        $request->setMethod('post');

        $this->setupOrganizationEntityRetrievalMocks($expected['organization']);
        $this->setupForwardPluginMock($result);

        $model = $this->target->acceptAction();

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $model);
        $variables = $model->getVariables();

        $this->assertEquals($expected, $variables);
    }

    private function setupAcceptInvitationHandlerMock($token, $result)
    {
        $this->queryParams['token'] = $token;

        $handler = $this->getMockBuilder('\Organizations\Controller\Plugin\AcceptInvitationHandler')
                        ->disableOriginalConstructor()->getMock();
        $handler->expects($this->once())->method('process')->with($token, $this->queryParams['organization'])
                ->willReturn($result);

        $this->pluginsMockMap['Organizations/AcceptInvitationHandler'] = $handler;
    }

    public function testAcceptActionReturnsSuccessViewModel()
    {
        $this->setupOrganizationEntityRetrievalMocks('testOrg');
        $this->setupAcceptInvitationHandlerMock('testToken', AcceptInvitationHandler::OK);

        $model = $this->target->acceptAction();

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $model);

        $this->assertEquals(array('organization' => 'testOrg'), $model->getVariables());


    }

    public function testAcceptActionReturnsSetPasswordViewModelOnRespHandlerResult()
    {
        $this->setupOrganizationEntityRetrievalMocks('testOrg');
        $this->setupAcceptInvitationHandlerMock('testToken', AcceptInvitationHandler::OK_SET_PW);
        $this->setupForwardPluginMock(array('form' => 'testForm'));

        $model = $this->target->acceptAction();

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $model);

        $this->assertEquals(array('organization' => 'testOrg', 'form' => 'testForm'), $model->getVariables());

    }

    public function provideAcceptActionReturnsErrorModelTestData()
    {
        return array(
            array(AcceptInvitationHandler::ERROR_ORGANIZATION_NOT_FOUND, 'The organization referenced in your request could not be found.'),
            array(AcceptInvitationHandler::ERROR_TOKEN_INVALID, 'The access token you provided seems to have expired.')
        );
    }

    /**
     * @dataProvider provideAcceptActionReturnsErrorModelTestData
     *
     * @param $handlerResult
     * @param $expectedMessage
     */
    public function testAcceptActionReturnsErrorViewModel($handlerResult, $expectedMessage)
    {
        $this->queryParams['organization'] = null;
        $this->setupAcceptInvitationHandlerMock('testToken', $handlerResult);

        $model = $this->target->acceptAction();

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $model);
        $this->assertEquals(500, $this->target->getResponse()->getStatusCode());
        $this->assertEquals('organizations/error/invite', $model->getTemplate());
        $this->assertEquals($expectedMessage, $model->getVariable('message'));
    }
}
