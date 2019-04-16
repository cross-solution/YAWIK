<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */
namespace CvFunctionalTest\Controller;

use PHPUnit\Framework\TestCase;

use Core\Repository\RepositoryService;
use CoreTestUtils\TestCase\FunctionalTestCase;
use Cv\Entity\Cv;
use Zend\Http\PhpEnvironment\Request;

/**
 * Class ManageControllerTest
 * @package CvTest\Controller
 * @ticket  227
 * @covers \Cv\Controller\ManageController
 */
class ManageControllerTest extends FunctionalTestCase
{
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

    protected function setUp()
    {
        parent::setUp();
        if (!is_object($this->activeUser)) {
            $this->loginAsUser();
        }
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
                $this->getRepositories()->remove($document);
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

    /**
     * @depends testPostPreferredJob
     */
    public function testIndexAction()
    {
        $this->dispatch('/en/cvs', Request::METHOD_GET);

        $result = $this->getResponse()->getContent();

        $this->assertResponseStatusCode(200);
        $this->assertContains('list of all resumes', $result);
        $this->assertContains('SO23 9AX Winchester , Saint Georges Street', $result);
    }
}
