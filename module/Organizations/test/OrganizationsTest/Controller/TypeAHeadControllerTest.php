<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace OrganizationsTest\Controller;

use AuthTest\Entity\Provider\UserEntityProvider;
use CoreTest\Controller\AbstractControllerTestCase;
use Organizations\Repository;
use Organizations\Controller\TypeAHeadController;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Test\Bootstrap;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\PluginManager;
use Zend\Stdlib\Parameters;
use Zend\View\Model\JsonModel;

class RegisterControllerTest extends AbstractControllerTestCase
{
    /**
     * @var MockObject|Repository\Organization
     */
    private $organizationRepoMock;

    /**
     * @var MockObject|\Auth\Controller\Plugin\Auth
     */
    private $authControllerPluginMock;

    public function setUp()
    {
        $this->init('typeAHead');

        $sm = clone Bootstrap::getServiceManager();

        $this->organizationRepoMock = $this->getMockBuilder('Organizations\Repository\Organization')
            ->disableOriginalConstructor()
            ->getMock();

        $this->authControllerPluginMock = $this->getMockBuilder('Auth\Controller\Plugin\Auth')
            ->setMethods(array('__invoke'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new TypeAHeadController($this->organizationRepoMock);
        $this->controller->setEvent($this->event);

        /** @var PluginManager $controllerPluginManager */
        $controllerPluginManager = $sm->get('ControllerPluginManager');
        $controllerPluginManager->setAllowOverride(true);
        $controllerPluginManager->setService('auth', $this->authControllerPluginMock);
        $this->controller->setPluginManager($controllerPluginManager);
    }

    public function testAction()
    {
        $queryName = uniqid('query');
        $data = array(
            mt_rand(1, 100) => array(
                'organizationName' => array(
                    'name' => uniqid('name')
                ),
                'contact' => array(
                    'city' => uniqid('city'),
                    'street' => uniqid('street'),
                    'houseNumber' => mt_rand(1, 100)
                )
            )
        );
        $user = UserEntityProvider::createEntityWithRandomData();

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->setQuery(new Parameters(array('q' => $queryName)));

        $this->authControllerPluginMock->expects($this->once())
            ->method('__invoke')
            ->with('id')
            ->willReturn($user->getId());

        $this->organizationRepoMock->expects($this->once())
            ->method('getTypeAheadResults')
            ->with($queryName, $user->getId())
            ->willReturn($data);

        $result = $this->controller->dispatch($request);
//        $expected = new JsonModel($data);

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
//        $this->assertSame($expected, $result);
    }

}