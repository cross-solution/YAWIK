<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTestUtils\TestCase;

use PHPUnit\Framework\TestCase;

use Auth\Entity\AnonymousUser;
use Auth\Entity\Info;
use Auth\Entity\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use CoreTest\Bootstrap;
use Zend\ServiceManager\ServiceManager;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class FunctionalTestCase
 * Provide Base Test for all database/controller related tests
 *
 * @TODO    Find a way to merge class with CoreTest\Controller\AbstractFunctionalControllerTestCase
 * @package CoreTestUtils\TestCase
 */
class FunctionalTestCase extends AbstractHttpControllerTestCase
{
    /**
     * @var User
     */
    protected $activeUser;

    /**
     * @var ServiceManager
     */
    protected $serviceLocator;

    protected function setUp(): void
    {
        $this->serviceLocator = null;
        $this->setApplicationConfig(
            Bootstrap::getConfig()
        );
        parent::setUp();

        if (!is_object($this->serviceLocator)) {
            $this->serviceLocator = $this->getApplicationServiceLocator();
            $this->serviceLocator->setAllowOverride(true);
        }
    }

    public function loginAsUser()
    {
        $user = $this->createUser(User::ROLE_USER);
        $this->authenticate($user);
    }

    /**
     * @param bool $hasIdentity
     * @param User $userEntity
     */
    protected function authenticate(User $userEntity = null)
    {
        if (null === $userEntity) {
            $userEntity = new AnonymousUser();
        }

        $authMock = $this->getMockBuilder('Auth\AuthenticationService')
            ->disableOriginalConstructor()
            ->getMock();

        $authMock->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $authMock->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($userEntity));

        $authMock->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($userEntity));

        $this->getApplicationServiceLocator()->setService('AuthenticationService', $authMock);
    }

    protected function createUser($role = User::ROLE_RECRUITER)
    {
        $email = 'test@yawik.org';
        $locator = $this->getApplicationServiceLocator();
        /* @var DocumentManager $dm */
        $dm = $locator->get('doctrine.documentmanager.odm_default');
        $userRepo = $dm->getRepository('Auth\Entity\User');

        $user = $userRepo->findOneBy(array(
            'login' => $email
        ));
        if (empty($user)) {
            $user = new User();
            $user
                ->setEmail($email)
                ->setLogin($email)
                ->setRole($role)
                ->setPassword($email);
            $infoEntity = new Info();
            $infoEntity->setEmail($email);
            $user->setInfo($infoEntity);
            $dm->persist($user);
        } else {
            $user->setRole($role);
        }
        $dm->flush();

        $this->activeUser = $user;
        return $user;
    }

    /**
     * @return DocumentManager
     */
    protected function getDoctrine()
    {
        return $this->getApplicationServiceLocator()->get('doctrine.documentmanager.odm_default');
    }
}
