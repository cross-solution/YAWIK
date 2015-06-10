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
        $logDir = __DIR__ . '/../../../../../log/';
        $errorLogFile = $logDir . 'error.log';
        $yawikLogFile = $logDir . 'yawik.log';

        if ((file_exists($errorLogFile) && !is_writable($errorLogFile))
            || (file_exists($yawikLogFile) && !is_writable($yawikLogFile))
        ) {
            $this->markTestSkipped('error.log and/or yawik.log is/are not writable! Run the test with the right user or set appropriate file permissions');
        }

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