<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Paginator;

use Solr\Bridge\Manager;
use Solr\Filter\JobBoardPaginationQuery;
use Solr\Paginator\Adapter\SolrAdapter;
use Solr\Paginator\JobsBoardPaginatorFactory;
use Zend\Filter\FilterPluginManager;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Paginator;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class JobsPaginatorFactoryTest
 * @package SolrTest\Paginator
 * @covers  Solr\Paginator\JobsBoardPaginatorFactory
 * @covers  Solr\Paginator\PaginatorFactoryAbstract
 */
class JobsPaginatorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $filterManager = $this->getMockBuilder(FilterPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $paginationQuery = $this->getMockBuilder(JobBoardPaginationQuery::class)
            ->getMock()
        ;
        $filterManager
            ->expects($this->once())
            ->method('get')
            ->with('Solr/Jobs/PaginationQuery')
            ->willReturn($paginationQuery)
        ;

        $solrManager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $sl = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['getServiceLocator','get','has'])
            ->getMock()
        ;
        $sl->method('getServiceLocator')->willReturn($sl);
        $sl->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['filterManager'],['Solr/Manager'])
            ->willReturnOnConsecutiveCalls($filterManager,$solrManager)
        ;

        $target = new JobsBoardPaginatorFactory();
        $target->setCreationOptions(['name'=>'value']);
        $retVal = $target->createService($sl);
        $this->assertInstanceOf(
            Paginator::class,
            $retVal,
            '::createService should return paginator service'
        );

        $this->assertInstanceOf(
            SolrAdapter::class,
            $retVal->getAdapter(),
            '::createService should create paginator with SolrAdapter as adapter'
        );

        $this->assertEquals(
            [],
            $target->getCreationOptions(),
            '::createService should empty creationOptions when the service created'
        );
    }
}
