<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace AuthTest\Controller;

use Auth\Adapter\HybridAuth;
use Core\Repository\RepositoryService;
use CoreTest\Controller\AbstractFunctionalControllerTestCase;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;


/**
 * Class ManageControllerTest
 
 * @package AuthTest\Controller
 */
class ManageControllerTest extends AbstractFunctionalControllerTestCase
{
	const URL_MY_PROFILE = '/en/my/profile';
	
	private $hybridAuthAdapter;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->hybridAuthAdapter = $this->getMockBuilder(HybridAuth::class)
			->disableOriginalConstructor()
			->getMock()
		;
		$this->setMockToServiceLocator('HybridAuthAdapter',$this->hybridAuthAdapter);
	}
	
	public function testAccessWhenNotLoggedIn()
	{
		$this->dispatch(self::URL_MY_PROFILE, Request::METHOD_GET);
		
		$result = $this->getResponse()->getContent();
		
		$this->assertNotRedirect();
		$this->assertResponseStatusCode(Response::STATUS_CODE_401);
		$this->assertContains('Please authenticate yourself to proceed', $result);
	}
	
	public function testAccessWhenLoggedIn()
	{
		$this->authenticateUser();
		$this->dispatch(self::URL_MY_PROFILE,Request::METHOD_GET);
		$result = $this->getResponse()->getContent();
		$this->assertNotRedirect();
		$this->assertResponseStatusCode(Response::STATUS_CODE_200);
		$this->assertContains('My profile - YAWIK', $result);
	}
}
