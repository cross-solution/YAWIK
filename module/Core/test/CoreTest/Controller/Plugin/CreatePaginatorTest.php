<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Controller\Plugin;

use PHPUnit\Framework\TestCase;

use Core\Controller\Plugin\CreatePaginator;
use Core\Listener\Events\CreatePaginatorEvent;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

/**
 * Tests for \Core\Controller\Plugin\CreatePaginator
 *
 * @covers \Core\Controller\Plugin\CreatePaginator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 *
 * @group Core
 * @group Core.Controller
 * @group Core.Controller.Plugin
 */
class CreatePaginatorTest extends TestCase
{

    /**
     * @testdox Extends \Zend\Mvc\Controller\Plugin\AbstractPlugin
     */
    public function testExtendsAbstractControllerPlugin()
    {
        $serviceManager = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
            ->disableOriginalConstructor()
            ->getMock();
        
        $target = new CreatePaginator($serviceManager, new Request());

        $this->assertInstanceOf('\Zend\Mvc\Controller\Plugin\AbstractPlugin', $target);
    }

    public function providePaginatorCreationData()
    {
        return [
            [ 'Test/Paginator', ['test' => 'value'], ['merged' => 'yes'], false, ['page' => 1, 'count' => 10, 'range' => 5] ],
            [ 'Test2/YetAnotherPager', ['page' => 2], [], true, ['page' => 2, 'count' => 10, 'range' => 5] ],
            [ 'Yet/Another', ['page' => 3, 'count' => 90, 'range' => 2], false, false, ['page' => 3, 'count' => 90, 'range' => 2] ],
            [ 'Even/Another', ['test' => 'value'], [], new \ArrayObject(['test' => 'value']), ['page' => 1, 'count' => 10, 'range' => 5]],
        ];
    }

    /**
     * @testdox Creates paginator via paginator service using request parameters.
     * @dataProvider providePaginatorCreationData
     *
     *
     * @param $paginatorName
     * @param $params
     * @param $defaultParams
     * @param $usePostParams
     * @param $expect
     */
    public function testPaginatorCreation($paginatorName, $params, $defaultParams, $usePostParams, $expect)
    {
        if ($defaultParams) {
            $options = array_merge($params, $defaultParams);
        } else {
            $options = $params;
        }
        $request = new Request();
        if ($usePostParams) {
            $request->setPost(new Parameters($params));
        } else {
            $request->setQuery(new Parameters($params));
        }
        
        
        $paginator = $this->getMockBuilder('\Zend\Paginator\Paginator')->disableOriginalConstructor()->getMock();
        $paginator->expects($this->once())->method('setCurrentPageNumber')->with($expect['page'])->will($this->returnSelf());
        $paginator->expects($this->once())->method('setItemCountPerPage')->with($expect['count'])->will($this->returnSelf());
        $paginator->expects($this->once())->method('setPageRange')->with($expect['range'])->will($this->returnSelf());

        $paginators = $this->getMockBuilder('\Core\Paginator\PaginatorService')->disableOriginalConstructor()->getMock();
        $paginators->expects($this->once())->method('get')->with($paginatorName, $options)->willReturn($paginator);

        $sm = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $event = $this->getMockBuilder(CreatePaginatorEvent::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $container = $this->getMockBuilder(ContainerInterface::class)
                          ->getMock();
        $container->expects($this->once())
            ->method('get')
            ->with('ServiceManager')
            ->willReturn($sm)
        ;
            
        $em = $this->getMockBuilder(EventManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEvent','triggerEvent'])
            ->getMock()
        ;
        
        $sm->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                ['Core/PaginatorService'],
                ['Core/CreatePaginator/Events']
            )
            ->willReturnOnConsecutiveCalls(
                $paginators,
                $em
            )
        ;

        // check if event create paginator is triggered
        $em->expects($this->once())
            ->method('getEvent')
            ->with(CreatePaginator::EVENT_CREATE_PAGINATOR)
            ->willReturn($event)
        ;
        $em->expects($this->once())
            ->method('triggerEvent')
            ->with($event)
        ;

        $target = new CreatePaginator($container, $request);

        $pager = false === $defaultParams ? $target($paginatorName, $usePostParams) : $target($paginatorName, $defaultParams, $usePostParams);

        $this->assertSame($paginator, $pager);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $defaultParams must be an array or implement \Traversable
     */
    public function testPassingInvalidDefaultParamsThrowsException()
    {
        $serviceManager = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
            ->disableOriginalConstructor()
            ->getMock();
        $target = new CreatePaginator($serviceManager, new Request());

        $target('NotNeeded', new \stdClass);
    }
}
