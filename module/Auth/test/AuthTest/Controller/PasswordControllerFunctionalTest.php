<?php

namespace AuthTest\Controller;

use CoreTest\Controller\AbstractFunctionalControllerTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;

class PasswordControllerFunctionalTest extends AbstractFunctionalControllerTestCase
{
    const URL_MY_PASSWORD = '/en/my/password';

    /**
     * @var MockObject
     */
    private $repositoriesMock;

    public function setUp()
    {
        parent::setUp();

        $this->repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setMockToServiceLocator('repositories', $this->repositoriesMock);
    }

    public function testAccessWhenYouAreNotLoggedIn()
    {
        $this->dispatch(self::URL_MY_PASSWORD, Request::METHOD_GET);

        $result = $this->getResponse()->getContent();

        $this->assertNotRedirect();
        $this->assertResponseStatusCode(Response::STATUS_CODE_403);
        $this->assertContains('Please authenticate yourself to proceed', $result);
    }

    public function testAccessWhenYouAreLogged()
    {
        $this->authenticateUser();
        $this->dispatch(self::URL_MY_PASSWORD, Request::METHOD_GET);

        $result = $this->getResponse()->getContent();

        $this->assertNotRedirect();
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertContains('My password', $result);
    }
}