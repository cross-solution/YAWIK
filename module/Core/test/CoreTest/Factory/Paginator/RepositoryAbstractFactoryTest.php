<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Factory\Paginator;

use PHPUnit\Framework\TestCase;

use Core\Factory\Paginator\RepositoryAbstractFactory;
use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Tests for \Core\Factory\Paginator\RepositoryAbstractFactory
 *
 * @covers \Core\Factory\Paginator\RepositoryAbstractFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Factory
 * @group Core.Factory.Paginator
 */
class RepositoryAbstractFactoryTest extends TestCase
{
    protected $target;

    protected function setUp(): void
    {
        $this->target = new RepositoryAbstractFactory();
    }

    /**
     * @testdox Implements \Laminas\ServiceManager\AbstractFactoryInterface and use Laminas\ServiceManager\MutableCreationOptionsInterface
     */
    public function testImplementsInterfaces()
    {
        $this->assertInstanceOf('\Laminas\ServiceManager\Factory\AbstractFactoryInterface', $this->target);
    }

    public function serviceNamesProvider()
    {
        return [
            [ 'Repository/Core/File', true ],
            [ 'Core/File', true ],
            [ 'Repository/NoModuleWillBeEverCalledThat/SomeEntity', false ],
            [ 'ThisIsAHighlyUnCommonModuleName/ObscureEntity', false ],
        ];
    }

    /**
     * @testdox Determines wether or not a paginator service can be created.
     *
     * @dataProvider serviceNamesProvider
     *
     * @param $serviceName
     * @param $expected
     */
    public function testCanCreateService($serviceName, $expected)
    {
        $sm = $this->getMockBuilder(AbstractPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $method = "assert" . ($expected ? 'True' : 'False');

        $this->{$method}($this->target->canCreate($sm, $serviceName));
    }

    public function servicesProvider()
    {
        return [
            [ 'Repository/Core/TestEntity', '\Core\Entity\TestEntity', [], false ],
            [ 'Core/TestEntity', '\Core\Entity\TestEntity', [ 'testOption' => 'testValue' ], true ]
        ];
    }

    /**
     * @testdox Creates paginators.
     * @dataProvider servicesProvider
     *
     * @param $serviceName
     * @param $entityName
     * @param $options
     * @param $hasFilter
     */
    /*
    FIXME: odm-module-3 non compatible tests
    public function testCreateServiceWithName($serviceName, $entityName, $options, $hasFilter)
    {
        $cursor = $this->getMockBuilder('\Doctrine\ODM\MongoDB\Cursor')
            ->disableOriginalConstructor()->getMock();

        $q = $this->getMockBuilder('\Doctrine\MongoDB\Query\Query')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $q->expects($this->once())->method('execute')->willReturn($cursor);

        $qb = $this->getMockBuilder('\Doctrine\MongoDB\Query\Builder')->disableOriginalConstructor()->getMock();

        $qb->expects($this->atLeast(1))->method('find')->with($entityName);
        $qb->expects($this->once())->method('getQuery')->willReturn($q);

        $repositories = $this->getMockBuilder('\Core\Repository\RepositoryService')
                             ->disableOriginalConstructor()->getMock();

        $repositories->expects($this->once())->method('createQueryBuilder')->willReturn($qb);

        $filters = $this->getMockBuilder('\Laminas\ServiceManager\AbstractPluginManager')
                        ->disableOriginalConstructor()
                        ->setMethods(['has', 'get'])->getMockForAbstractClass();

        $filters->expects($this->once())->method('has')->with('PaginationQuery/' . $serviceName)->willReturn($hasFilter);

        if ($hasFilter) {
            $filter = $this->getMockBuilder('\Laminas\Filter\FilterInterface')->getMockForAbstractClass();
            $filter->expects($this->once())->method('filter')->with($options, $qb)->willReturn($qb);

            $filters->expects($this->once())->method('get')->with('PaginationQuery/' . $serviceName)->willReturn($filter);
        }

        $sm = $this->getMockBuilder('\Laminas\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();

        $sm->expects($this->exactly(2))->method('get')
                                       ->withConsecutive([ 'repositories' ], [ 'FilterManager'])
                                       ->will($this->onConsecutiveCalls($repositories, $filters));

        $target = $this->target;
        $paginator = $target($sm, $serviceName, $options);

        $this->assertInstanceOf('\Laminas\Paginator\Paginator', $paginator, 'No Paginator returned.');
        $adapter = $paginator->getAdapter();

        $this->assertInstanceOf('\Core\Paginator\Adapter\DoctrineMongoAdapter', $adapter, 'Adapter is not correct class instance.');
        $this->assertAttributeSame($cursor, 'cursor', $adapter, 'Adapter has gotten the wrong cursor.');
    }
    */
}
