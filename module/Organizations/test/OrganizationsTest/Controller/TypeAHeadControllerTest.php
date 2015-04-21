<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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

/**
 * @group Organizations
 * @group Organizations.Controller
 */
class TypeAHeadControllerTest extends AbstractControllerTestCase
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
            ->setMethods(array('__invoke', 'getUser'))
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

    /**
      */
    public function testRepositoryIsInjectedInConstructor()
    {
        $this->assertAttributeSame($this->organizationRepoMock, 'organizationRepository', $this->controller);
    }

    /**
     */
    public function testAction()
    {
        $queryName = uniqid('query');
        list($data, $expected) = $this->prepareDataAndExpected();
        $user = UserEntityProvider::createEntityWithRandomData();

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->setQuery(new Parameters(array('q' => $queryName)));

        $this->authControllerPluginMock->expects($this->once())
            ->method('__invoke')
            ->with(null)
            ->will($this->returnSelf());
        $this->authControllerPluginMock->expects($this->once())
            ->method('getUser')->willReturn($user);

        $this->organizationRepoMock->expects($this->once())
            ->method('getTypeAheadResults')
            ->with($queryName, $user)
            ->willReturn($data);

        /** @var JsonModel $result */
        $result = $this->controller->dispatch($request);

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result->getVariables());
    }

    private function prepareDataAndExpected()
    {
        $id = mt_rand(1, 100);
        $name = uniqid('name');
        $city = uniqid('city');
        $street = uniqid('street');
        $houseNumber = mt_rand(1, 100);

        $data = array(
            $id => array(
                'organizationName' => array(
                    'name' => $name
                ),
                'contact' => array(
                    'city' => $city,
                    'street' => $street,
                    'houseNumber' => $houseNumber
                )
            )
        );

        $expected = array(
            array(
                'id' => $id,
                'name' => $name,
                'city' => $city,
                'street' => $street,
                'houseNumber' => $houseNumber
            )
        );

        return array($data, $expected);
    }

}