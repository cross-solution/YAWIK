<?php

namespace CoreTest\Controller;

use Auth\Entity\AnonymousUser;
use Auth\Entity\User;
use AuthTest\Entity\Provider\UserEntityProvider;
use Test\Bootstrap;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\ServiceManager\ServiceManager;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;


/**
 * Class AbstractFunctionalControllerTestCase
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @package CoreTest\Controller
 */
abstract class AbstractFunctionalControllerTestCase extends AbstractHttpControllerTestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceLocator;

    /**
     * @var MockObject
     */
    private $documentManagerMock;

    public function setUp()
    {
        $this->serviceLocator = null;
        $this->setApplicationConfig(
            Bootstrap::getConfig()
        );
        parent::setUp();
        $this->prepareAuthenticateMock();
        $this->prepareDocumentManagerMock();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        Bootstrap::setupServiceManager();
    }

    /**
     * Set service to service locator
     *
     * @param string $name
     * @param object $object
     *
     * @return ServiceManager
     */
    protected function setMockToServiceLocator($name, $object)
    {
        if (!$this->serviceLocator) {
            $this->serviceLocator = $this->getApplicationServiceLocator();
            $this->serviceLocator->setAllowOverride(true);
        }
        $this->serviceLocator->setService($name, $object);
        return $this->serviceLocator;
    }

    /**
     * Creates and authenticates a user.
     *
     * @param array $params
     *
     * @return User
     */
    protected function authenticateUser(array $params = array())
    {
        $userEntity = UserEntityProvider::createEntityWithRandomData($params);
        $this->prepareAuthenticateMock(true, $userEntity);
        return $userEntity;
    }

    /**
     * @param bool $hasIdentity
     * @param User $userEntity
     */
    protected function prepareAuthenticateMock($hasIdentity = false, User $userEntity = null)
    {
        if (null === $userEntity) {
            $userEntity = new AnonymousUser();
        }

        $authMock = $this->getMockBuilder('Auth\AuthenticationService')
            ->disableOriginalConstructor()
            ->getMock();

        $authMock->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue($hasIdentity));

        $authMock->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($userEntity));

        $authMock->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($userEntity));

        $this->setMockToServiceLocator('AuthenticationService', $authMock);
    }

    protected function prepareDocumentManagerMock()
    {
        $this->documentManagerMock = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setMockToServiceLocator('Core\DocumentManager', $this->documentManagerMock);
    }
}