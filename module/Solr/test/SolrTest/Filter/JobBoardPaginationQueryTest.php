<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Filter;

use Jobs\Entity\CoordinatesInterface;
use Jobs\Entity\JobInterface;
use Jobs\Entity\Location;
use Solr\Bridge\Manager;
use Solr\Filter\JobBoardPaginationQuery;
use Zend\ServiceManager\ServiceLocatorInterface;
use Solr\Entity\JobProxy;
use ArrayObject;

/**
 * Class JobBoardPaginationQueryTest
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @author  Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author  Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since   0.26
 * @package SolrTest\Filter
 * @covers  Solr\Filter\JobBoardPaginationQuery
 * @covers  Solr\Filter\AbstractPaginationQuery
 * @requires extension solr
 */
class JobBoardPaginationQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JobBoardPaginationQuery
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
        $this->target = new JobBoardPaginationQuery;
        $this->manager = $manager;
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
        $query  = $this->getMockBuilder(\SolrDisMaxQuery::class)
            ->setMethods([
                'setQuery',
                'addFilterQuery',
                'addField',
                'addParam',
                'setFacet',
                'addFacetField',
                'addFacetDateField',
                'setHighlight',
                'addHighlightField',
            ])
            ->getMock()
        ;
        $coordinates = $this->getMockBuilder(CoordinatesInterface::class)
            ->getMock()
        ;
        $location = $this->getMockBuilder(Location::class)
            ->getMock()
        ;
        $location->method('getCoordinates')->willReturn($coordinates);
        $coordinates->expects($this->once())
            ->method('getCoordinates')
            ->willReturn([1.2,2.1])
        ;

        // expect to setQuery
        $query
            ->expects($this->exactly(2))
            ->method('setQuery')
            ->withConsecutive(['*:*'],['some'])
        ;

        // expect to handle location
        $query
            ->expects($this->exactly(5))
            ->method('addFilterQuery')
            ->withConsecutive(['entityName:job'],['isActive:1'],['entityName:job'],['isActive:1'],[$this->stringContains('{!geofilt pt=1.2,2.1 sfield=point d=10 score="kilometers"}')])
        ;

        $query->method('addField')->willReturn($query);

        $query->expects($this->exactly(2))->method('setFacet')->with(true)->will($this->returnSelf());
        $query->expects($this->exactly(2))->method('addFacetField')->with('regionList')->will($this->returnSelf());
        $query->expects($this->exactly(2))->method('addFacetDateField')->with('datePublishStart')->will($this->returnSelf());

        $query->expects($this->exactly(2))->method('setHighlight')->with(true)->will($this->returnSelf());
        $query->expects($this->exactly(2))->method('addHighlightField')->with('title')->will($this->returnSelf());

        $params1 = ['search' => '','sort'=>'title'];
        $params2 = ['search' => 'some','sort'=>'-company','location'=>$location,'d'=>10];
        $target = $this->target;
        $target->createQuery($params1,$query);
        $actual = $target->createQuery($params2,$query);

        $this->assertSame($query, $actual);
    }
    
    public function testProxyFactory()
    {
        $entity = $this->getMockBuilder(JobInterface::class)
            ->getMock();
        
        $this->assertInstanceOf(JobProxy::class, $this->target->proxyFactory($entity, new ArrayObject()));
    }
    
    public function testGetRepositoryName()
    {
        $this->assertSame('Jobs/Job', $this->target->getRepositoryName());
    }
}
