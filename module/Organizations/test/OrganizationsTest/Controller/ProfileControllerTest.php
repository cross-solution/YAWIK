<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace OrganizationsTest\Controller;

use Core\Entity\Exception\NotFoundException;
use CoreTest\Controller\AbstractControllerTestCase;
use Interop\Container\ContainerInterface;
use Organizations\Controller\ProfileController;
use Organizations\Entity\Organization;
use Zend\I18n\Translator\TranslatorInterface;
use Organizations\Repository\Organization as OrganizationRepository;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\PluginManager;
use Zend\View\Model\ViewModel;

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
    private $container;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $translator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    public function setUp()
    {
        $container = $this->createMock(ContainerInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $repository = $this->createMock(OrganizationRepository::class);

        $container->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['repositories',$container],
                ['Organizations/Organization',$repository],
                ['translator',$translator]
            ])
        ;

        $this->target = ProfileController::factory($container);
        $this->container = $container;
        $this->translator = $translator;
        $this->repository = $repository;
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
        $return = $target->indexAction();
        $this->assertArrayHasKey('message',$return);
        $this->assertArrayHasKey('exception',$return);
        $this->assertRegExp(
            '/Null Organization/',
            $return['exception']->getMessage())
        ;
    }

    public function testIndexThrowNotFoundWhenOrganizationIsNotFound()
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
            ->with('id',null)
            ->willReturn('some-id')
        ;

        $target = $this->target;
        $target->setPluginManager($plugins);
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessageRegExp('/some-id/');
        $target->indexAction();
    }

    public function testIndexShouldReturnOrganizationWithGivenId()
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
            ->with('id',null)
            ->willReturn('some-id')
        ;

        $entity = new Organization();
        $this->repository->expects($this->once())
            ->method('find')
            ->with('some-id')
            ->willReturn($entity)
        ;

        $target = $this->target;
        $target->setPluginManager($pluginManager);

        /* @var \Zend\View\Model\ViewModel $retVal */
        $retVal = $target->indexAction();
        $this->assertInstanceOf(ViewModel::class,$retVal);
        $this->assertArrayHasKey('organization',$retVal->getVariables());
        $this->assertEquals($entity,$retVal->getVariable('organization'));
    }
}
