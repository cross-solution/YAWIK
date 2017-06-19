<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Factory\Listener;

use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Factory\Listener\LoadActiveOrganizationsFactory;
use Jobs\Listener\LoadActiveOrganizations;
use Zend\Paginator\Paginator;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Jobs\Factory\Listener\LoadActiveOrganizationsFactory
 * 
 * @covers \Jobs\Factory\Listener\LoadActiveOrganizationsFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.Listener
 */
class LoadActiveOrganizationsFactoryTest extends \PHPUnit_Framework_TestCase
{

    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|LoadActiveOrganizationsFactory
     */
    private $target = [
        LoadActiveOrganizationsFactory::class,
        '@testCreateService' => ['mock' => ['__invoke' => ['count' => 1]]],
    ];

    private $inheritance = [ FactoryInterface::class ];
	

    public function testServiceCreation()
    {
        $paginator = new Paginator(new \Zend\Paginator\Adapter\NullFill());
        $paginators = $this->getPluginManagerMock([
                'Jobs\Paginator\ActiveOrganizations' => $paginator,
            ]);
        $container = $this->createServiceManagerMock(['Core/PaginatorService' => $paginators]);

        $listener = $this->target->__invoke($container, 'irrelevant');

        $this->assertInstanceOf(LoadActiveOrganizations::class, $listener);
    }
}