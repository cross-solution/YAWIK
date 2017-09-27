<?php

namespace AuthTest\Controller;

use Auth\Repository\User;
use CoreTest\Controller\AbstractFunctionalControllerTestCase;
use Organizations\ImageFileCache\Manager;
use Organizations\Repository\OrganizationImage;
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

        $orgImageRepo = $this->getMockBuilder(OrganizationImage::class)->disableOriginalConstructor()->getMock();
        $this->repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();
        $this->repositoriesMock->expects($this->any())->method('get')
            ->will($this->returnValueMap([
                [ 'Organizations/OrganizationImage', $orgImageRepo ]
            ]));
	    
        $manager = $this->getMockBuilder(Manager::class)
	        ->disableOriginalConstructor()
	        ->getMock()
	    ;
        $hybridAuth = $this->getMockBuilder(\Hybrid_Auth::class)->disableOriginalConstructor()->getMock();
        $this->setMockToServiceLocator('repositories', $this->repositoriesMock);
        $this->setMockToServiceLocator('Organizations\ImageFileCache\Manager',$manager);
        $this->setMockToServiceLocator('HybridAuth', $hybridAuth);
    }

    /**
     * This is needed. Otherwise the test fill fail with an Auth\Exception\UnauthorizedAccessException
     */
    public function tearDown()
    {
    }

    public function testAccessWhenYouAreLogged()
    {
	    /*$repository = $this->getMockBuilder(OrganizationImage::class)
	                       ->disableOriginalConstructor()
	                       ->getMock()
	    ;
	    $this->repositoriesMock
		    ->expects($this->once())
		    ->method('get')
		    ->with('Organizations/OrganizationImage')
		    ->willReturn($repository)
	    ;*/
	    
	    
        $this->authenticateUser();
        $this->dispatch(self::URL_MY_PASSWORD, Request::METHOD_GET);
        $result = $this->getResponse()->getContent();

        $this->assertNotRedirect();
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertContains('My password', $result);
    }
	
	public function testAccessWhenYouAreNotLoggedIn()
	{
		$this->dispatch(self::URL_MY_PASSWORD, Request::METHOD_GET);
		
		$result = $this->getResponse()->getContent();
		
		$this->assertNotRedirect();
		$this->assertResponseStatusCode(Response::STATUS_CODE_401);
		$this->assertContains('Please authenticate yourself to proceed', $result);
	}
	
	/**
	 * Assert response status code
	 *
	 * @param int $code
	 */
	public function assertResponseStatusCode($code)
	{
		if ($this->useConsoleRequest) {
			if (! in_array($code, [0, 1])) {
				throw new \PHPUnit_Framework_ExpectationFailedException($this->createFailureMessage(
					'Console status code assert value must be O (valid) or 1 (error)'
				));
			}
		}
		$match = $this->getResponseStatusCode();
		if ($code != $match) {
			throw new \PHPUnit_Framework_ExpectationFailedException($this->createFailureMessage(
				sprintf('Failed asserting response code "%s", actual status code is "%s"', $code, $match)
			));
		}
		$this->assertEquals($code, $match);
	}
}
