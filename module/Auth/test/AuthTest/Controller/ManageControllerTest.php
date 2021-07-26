<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace AuthTest\Controller;

use PHPUnit\Framework\TestCase;

use Auth\Adapter\HybridAuth;
use Core\Repository\RepositoryService;
use CoreTest\Controller\AbstractFunctionalControllerTestCase;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Http\PhpEnvironment\Response;

/**
 * Class ManageControllerTest

 * @package AuthTest\Controller
 */
class ManageControllerTest extends AbstractFunctionalControllerTestCase
{
    const URL_MY_PROFILE = '/en/my/profile';

    private $hybridAuthAdapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hybridAuthAdapter = $this->getMockBuilder(HybridAuth::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->setMockToServiceLocator('HybridAuthAdapter', $this->hybridAuthAdapter);
    }

    public function testAccessWhenNotLoggedIn()
    {
        $this->dispatch(self::URL_MY_PROFILE, Request::METHOD_GET);

        $result = $this->getResponse()->getContent();

        $this->assertRedirect();
        $this->assertResponseStatusCode(Response::STATUS_CODE_303);
        $this->assertRedirectRegex('~/login\?ref=.*?profile~');
    }

    public function testAccessWhenLoggedIn()
    {
        $this->authenticateUser();
        $this->dispatch(self::URL_MY_PROFILE, Request::METHOD_GET);
        $result = $this->getResponse()->getContent();
        $this->assertNotRedirect();
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertContains('My profile - YAWIK', $result);
    }
}
