<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace OrganizationsTest\Controller;

use Auth\Exception\UnauthorizedAccessException;
use Core\Controller\Plugin\PaginationBuilder;
use Core\Entity\Exception\NotFoundException;
use CoreTest\Controller\AbstractControllerTestCase;
use Jobs\Repository\Job as JobRepository;
use Organizations\Controller\ProfileController;
use Organizations\Entity\Organization;
use Zend\I18n\Translator\TranslatorInterface;
use Organizations\Repository\Organization as OrganizationRepository;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\PluginManager;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use Organizations\ImageFileCache\Manager as ImageFileCacheManager;

/**
 * Class ProfileControllerTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package OrganizationsTest\Controller
 * @since 0.30
 * @covers \Organizations\Controller\ProfileController
 */
class ProfileControllerTest extends AbstractControllerTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $target;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $translator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $jobRepository;

    private $imageFileCacheManager;

    public function setUp()
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $repository = $this->createMock(OrganizationRepository::class);
        $jobRepository = $this->createMock(JobRepository::class);
        $imageFileCacheManager = $this->createMock(ImageFileCacheManager::class);
        $options = ['count' => 12];

        $this->target = new ProfileController(
            $repository,
            $jobRepository,
            $translator,
            $imageFileCacheManager,
            $options
        );

        $this->translator = $translator;
        $this->repository = $repository;
        $this->jobRepository = $jobRepository;
        $this->imageFileCacheManager = $imageFileCacheManager;
    }

    public function testIndexAction()
    {
        $pluginManager = $this->createMock(PluginManager::class);

        $pluginManager->expects($this->once())
            ->method('get')
            ->with('pagination')
            ->willReturn(['foo' => 'bar'])
        ;
        $target = $this->target;
        $target->setPluginManager($pluginManager);

        /* @var ViewModel $result */
        $result = $target->indexAction();
        $this->assertArrayHasKey('foo', $result->getVariables());
        $this->assertEquals('bar', $result->getVariable('foo'));
    }

    public function testIndexThrowsExceptionOnNullID()
    {
        $pluginManager = $this->createMock(PluginManager::class);
        $params = $this->createMock(Params::class);
        $pluginManager->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['params',null,$params]
            ])
        ;

        $target = $this->target;
        $target->setPluginManager($pluginManager);
        $return = $target->detailAction();
        $this->assertArrayHasKey('message', $return);
        $this->assertArrayHasKey('exception', $return);
        $this->assertRegExp(
            '/Null Organization/',
            $return['exception']->getMessage()
        )
        ;
    }

    public function testDetailThrowNotFoundWhenOrganizationIsNotFound()
    {
        $plugins = $this->createMock(PluginManager::class);
        $params = $this->createMock(Params::class);
        $plugins->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['params',null,$params]
            ])
        ;

        $params->expects($this->any())
            ->method('__invoke')
            ->with('id', null)
            ->willReturn('some-id')
        ;

        $target = $this->target;
        $target->setPluginManager($plugins);
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessageRegExp('/some-id/');
        $target->detailAction();
    }

    public function testIndexShouldRenderDisabledProfile()
    {
        $plugins = $this->createMock(PluginManager::class);
        $params = $this->createMock(Params::class);
        $plugins->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['params',null,$params]
            ])
        ;

        $params->expects($this->any())
            ->method('__invoke')
            ->with('id', null)
            ->willReturn('some-id')
        ;

        $organization = new Organization();
        $organization->setProfileSetting(Organization::PROFILE_DISABLED);
        $repo = $this->repository;
        $repo->expects($this->once())
            ->method('find')
            ->with('some-id')
            ->willReturn($organization)
        ;
        $target = $this->target;
        $target->setPluginManager($plugins);
        /* @var ViewModel $result */
        $result = $target->detailAction();
        $this->assertEquals('organizations/profile/disabled', $result->getTemplate());
    }

    public function testDetailShouldReturnOrganizationWithGivenId()
    {
        $pluginManager = $this->createMock(PluginManager::class);
        $params = $this->createMock(Params::class);
        $pluginManager->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['params',null,$params]
            ])
        ;

        $params->expects($this->any())
            ->method('__invoke')
            ->with('id', null)
            ->willReturn('some-id')
        ;

        $entity = new Organization();
        $entity->setProfileSetting(Organization::PROFILE_ALWAYS_ENABLE);
        $this->repository->expects($this->once())
            ->method('find')
            ->with('some-id')
            ->willReturn($entity)
        ;

        $target = $this->target;
        $target->setPluginManager($pluginManager);

        /* @var \Zend\View\Model\ViewModel $retVal */
        $retVal = $target->detailAction();
        $this->assertInstanceOf(ViewModel::class, $retVal);
        $this->assertArrayHasKey('organization', $retVal->getVariables());
        $this->assertEquals($entity, $retVal->getVariable('organization'));
    }

    public function testDetailShouldRenderDisabledProfileWhenNoActiveJobs()
    {
        $pluginManager = $this->createMock(PluginManager::class);
        $params = $this->createMock(Params::class);
        $pluginManager->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['params',null,$params]
            ])
        ;

        $params->expects($this->any())
            ->method('__invoke')
            ->with('id', null)
            ->willReturn('some-id')
        ;

        $entity = new Organization();
        $entity->setProfileSetting(Organization::PROFILE_ACTIVE_JOBS);
        $this->repository->expects($this->once())
            ->method('find')
            ->with('some-id')
            ->willReturn($entity)
        ;

        $target = $this->getMockBuilder(ProfileController::class)
            ->setConstructorArgs([
                $this->repository,
                $this->jobRepository,
                $this->translator,
                $this->imageFileCacheManager,
                ['count' => 10],
            ])
            ->setMethods(['pagination'])
            ->getMock()
        ;

        $paginator = $this->createMock(Paginator::class);
        $paginator->expects($this->once())
            ->method('getTotalItemCount')
            ->willReturn(0)
        ;
        $target->expects($this->once())
            ->method('pagination')
            ->willReturn([
                'jobs' => $paginator
            ])
        ;
        $target->setPluginManager($pluginManager);

        $result = $target->detailAction();
        $this->assertEquals('organizations/profile/disabled', $result->getTemplate());
    }
}
