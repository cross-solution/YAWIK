<?php
/**
 * Created by PhpStorm.
 * User: toni
 * Date: 16/06/16
 * Time: 12:17
 */

namespace CvTest\Controller;

use Auth\Entity\Info;
use Auth\Entity\User;
use Core\Repository\RepositoryService;
use Cv\Entity\Cv;
use Test\Bootstrap;
use Zend\Http\PhpEnvironment\Request;
use CoreTest\Controller\AbstractFunctionalControllerTestCase;
use Zend\Console\Console;

/**
 * Class ManageControllerTest
 * @covers  \Cv\Controller\ManageController
 * @package CvTest\Controller
 * @ticket  227
 */
class ManageControllerTest extends AbstractFunctionalControllerTestCase
{
    protected $repositoriesMock;

    protected $testData = [
        'preferredJob' => [
            'typeOfApplication' => ['temporary'],
            'desiredJob' => 'Software Developer',
            'geo-location' => [
                'name' => 'SO23 9AX Winchester , Saint Georges Street',
                'data' => '{"geometry":{"coordinates":[-1.312906,51.0626241],"type":"Point"},"type":"Feature","properties":{"osm_id":503417696,"osm_type":"N","country":"United Kingdom","osm_key":"highway","city":"Winchester","street":"Saint Georges Street","osm_value":"bus_stop","postcode":"SO23 9AX","name":"Winchester Photographic","state":"England"}}',
                'type' => 'photon'
            ],
            'willingnessToTravel' => 'yes',
            'expectedSalary' => 20000
        ]
    ];

    /**
     * @var User
     */
    protected $activeUser;

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

        $this->serviceLocator = null;
        $this->setApplicationConfig(
            Bootstrap::getConfig()
        );

        $this->usedConsoleBackup = Console::isConsole();
        $this->reset();

        $this->loginAsUser();
    }

    protected function createUser($role = User::ROLE_RECRUITER)
    {
        $email = 'test@yawik.org';
        $locator = $this->getApplicationServiceLocator();
        /* @var \Core\Repository\RepositoryService $repo */
        $repo = $locator->get('repositories');
        $userRepo = $repo->get('Auth/User');

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
            $repo->store($user);
        }

        $user->setRole($role);
        $repo->store($user);
        $repo->flush($user);

        $this->activeUser = $user;
        return $user;
    }

    public function loginAsUser()
    {
        $user = $this->createUser(User::ROLE_USER);
        $this->prepareAuthenticateMock(true, $user);
    }

    /**
     * @return RepositoryService
     */
    public function getRepositories()
    {
        return $this->getApplicationServiceLocator()->get('repositories');
    }

    public function testCreateAction()
    {
        // empty the cv draft first
        $cvRepo = $this->getRepositories()->get('Cv/Cv');
        $documents = $cvRepo->findBy(array(
            'user' => $this->activeUser->getId(),
        ));
        if (!empty($documents)) {
            foreach ($documents as $document) {
                $this->getRepositories()->detach($document);
                $this->getRepositories()->flush();
            }
        }

        $this->dispatch('/en/cvs/create', Request::METHOD_GET);

        $result = $this->getResponse()->getContent();
        $this->assertContains('Create a new resume', $result);
    }

    /**
     * @depends testCreateAction
     */
    public function testPostPreferredJob()
    {
        $data = $this->testData;
        $this->dispatch('/en/cvs/create?form=preferredJob', Request::METHOD_POST, $data);

        /* @var \Cv\Repository\Cv $repo */
        $repo = $this->getRepositories()->get('Cv/Cv');
        /* @var \Cv\Entity\Cv $cv */
        $cv = $repo->findDraft($this->activeUser);

        $this->assertInstanceOf(
            Cv::class,
            $cv
        );
        $this->assertEquals($data['preferredJob']['desiredJob'], $cv->getPreferredJob()->getDesiredJob());
        $this->assertEquals($data['preferredJob']['geo-location']['name'], $cv->getPreferredJob()->getDesiredLocation());
    }
}
