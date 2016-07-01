<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Filter;

use Solr\Bridge\Manager;
use Solr\Filter\JobBoardPaginationQuery;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class JobBoardPaginationQueryTest
 * @package SolrTest\Filter
 * @covers  Solr\Filter\JobBoardPaginationQuery
 * @covers  Solr\Filter\AbstractPaginationQuery
 */
class JobBoardPaginationQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $target;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    public function setUp()
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $sl = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get','has','getServiceLocator'])
            ->getMock()
        ;
        $sl->method('getServiceLocator')->willReturn($sl);
        $sl->method('get')->with('Solr/Manager')->willReturn($manager);
        $this->target = JobBoardPaginationQuery::factory($sl);
        $this->manager = $manager;
    }

    public function testFactory()
    {
        $target = $this->target;
        $this->assertInstanceOf(
            JobBoardPaginationQuery::class,
            $target,
            '::factory should return a correct instance'
        );
    }

    public function testFilter()
    {
        $this->assertInstanceOf(
            \SolrQuery::class,
            $this->target->filter([]),
            '::filter should return a \SolrQuery object'
        );
    }

    public function testCreateQuery()
    {
        $query  = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['setQuery','addSortField'])
            ->getMock()
        ;

        $query
            ->expects($this->exactly(2))
            ->method('setQuery')
            ->withConsecutive(['*:*'],['title:some OR organizationName:some'])
        ;
        
        $query
            ->expects($this->exactly(2))
            ->method('addSortField')
            ->withConsecutive(
                ['title',Manager::SORT_ASCENDING],
                ['companyName',Manager::SORT_DESCENDING]
            )
        ;

        $target = $this->target;
        $target->createQuery(['search' => '','sort'=>'title'],$query);
        $target->createQuery(['search' => 'some','sort'=>'-company'],$query);
    }
    
    public function testDelete()
    {
        
    }
}
