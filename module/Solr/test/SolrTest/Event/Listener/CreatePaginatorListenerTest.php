<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Event\Listener;


use Core\Controller\Plugin\CreatePaginator;
use Core\Paginator\PaginatorService;
use Solr\Event\Listener\CreatePaginatorListener;
use Solr\Paginator\PaginatorFactoryAbstract;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\SharedEventManagerInterface;

class CreatePaginatorListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Class under test
     * @var CreatePaginatorListener
     */
    protected $target;

    /**
     * Mock for PaginatorService
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $paginators;

    public function setUp()
    {
        $mock = $this->getMockBuilder(PaginatorService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->target = new CreatePaginatorListener($mock);
        $this->paginators = $mock;
    }

    public function testAttachAndDetachEvent()
    {
        $sharedManager = $this->getMockBuilder(SharedEventManagerInterface::class)
            ->getMock()
        ;
        $events = $this->getMockBuilder(EventManagerInterface::class)
            ->getMock()
        ;
        $events
            ->expects($this->once())
            ->method('getSharedManager')
            ->willReturn($sharedManager)
        ;

        // attach expectation
        $sharedManager
            ->expects($this->once())
            ->method('attach')
            ->with('*',CreatePaginator::EVENT_CREATE_PAGINATOR,[$this->target,'onCreatePaginator'],1)
            ->willReturn([])
        ;

        //detach expectation
        $events->expects($this->once())
            ->method('detach')
            ->willReturn(true);
        ;

        $this->target->attach($events);
        $this->target->detach($events);
    }

    public function testOnCreatePaginator()
    {
        $paginators = $this->paginators;
        $event = $this->getMockBuilder(EventInterface::class)
            ->getMock()
        ;
        $abstractPaginator = $this->getMockBuilder(PaginatorFactoryAbstract::class)
            ->getMock()
        ;
        $event
            ->expects($this->any())
            ->method('getParams')
            ->willReturn([
                'paginatorName' => 'Some/Paginator',
                'params' => ['name'=>'value']
            ])
        ;
        $paginators
            ->expects($this->exactly(2))
            ->method('has')
            ->with('Solr/Some/Paginator')
            ->willReturnOnConsecutiveCalls(false,true)
        ;
        $this->assertFalse(
            $this->target->onCreatePaginator($event),
            '::onCreatePaginator should return false when solr paginator is not created'
        );

        $paginators
            ->expects($this->once())
            ->method('get')
            ->with('Solr/Some/Paginator',['name'=>'value'])
            ->willReturn($abstractPaginator)
        ;

        $this->assertEquals(
            $abstractPaginator,
            $this->target->onCreatePaginator($event),
            '::onCreatePaginator should return correct paginator service'
        );
    }
}
