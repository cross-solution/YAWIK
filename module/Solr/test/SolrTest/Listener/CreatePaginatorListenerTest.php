<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Listener;


use Core\Listener\Events\CreatePaginatorEvent;
use Core\Paginator\PaginatorService;
use Solr\Listener\CreatePaginatorListener;
use Zend\Paginator\Paginator;

/**
 * Test for Solr\Listener\CreatePaginatorListener
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @covers  Solr\Listener\CreatePaginatorListener
 * @package SolrTest\Event\Listener
 */
class CreatePaginatorListenerTest extends \PHPUnit_Framework_TestCase
{

    public function testOnCreatePaginator()
    {
        $paginators = $this->getMockBuilder(PaginatorService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $event = $this->getMockBuilder(CreatePaginatorEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPaginators','getPaginatorParams','getPaginatorName'])
            ->getMock()
        ;

        $event->expects($this->exactly(2))
            ->method('getPaginatorParams')
            ->willReturn(['name'=>'value'])
        ;
        $event->expects($this->exactly(2))
            ->method('getPaginatorName')
            ->willReturn('Some/Paginator')
        ;
        $event->expects($this->exactly(2))
            ->method('getPaginators')
            ->willReturn($paginators)
        ;

        $paginator = $this->getMockBuilder(Paginator::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $paginators
            ->expects($this->exactly(2))
            ->method('has')
            ->with('Solr/Some/Paginator')
            ->willReturnOnConsecutiveCalls(false,true)
        ;

        $target = CreatePaginatorListener::factory();
        $target->onCreatePaginator($event);
        $this->assertNull(
            $event->getPaginator(),
            '::onCreatePaginator should return false when solr paginator is not created'
        );

        $paginators
            ->expects($this->once())
            ->method('get')
            ->with('Solr/Some/Paginator',['name'=>'value'])
            ->willReturn($paginator)
        ;
        $target->onCreatePaginator($event);
        $this->assertEquals(
            $paginator,
            $event->getPaginator(),
            '::onCreatePaginator should return correct paginator service'
        );
    }
}
