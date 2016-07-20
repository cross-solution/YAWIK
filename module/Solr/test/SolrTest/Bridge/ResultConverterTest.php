<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Bridge;

use Core\Repository\RepositoryService;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Query;
use Jobs\Entity\Job;
use Jobs\Entity\JobInterface;
use Solr\Bridge\Manager;
use Solr\Bridge\ResultConverter;
use Solr\Filter\AbstractPaginationQuery;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResultDocument
{
    public $id;

    public $title;

    public $customField;

    public $dateCreated;

    public function __construct($propsValue)
    {
        foreach($propsValue as $name=>$value){
            $this->$name = $value;
        }
    }

    public function getPropertyNames()
    {
        return array('id','title','dateCreated','customField');
    }
}

/**
 * Class ResultConverterTest
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @covers  Solr\Bridge\ResultConverter
 * @package SolrTest\Bridge
 */
class ResultConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Mock for AbstractPaginationQuery
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filter;

    /**
     * Mock for ResultConverter
     *
     * @var ResultConverter
     */
    protected $target;

    /**
     * Mock for SolrQueryResponse
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryResponse;

    /**
     * Mock for SolrQueryObject
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sl;

    public function setUp()
    {
        $queryResponse = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['getResponse'])
            ->getMock()
        ;

        $response = $this->getMockBuilder(\ArrayAccess::class)
            ->setMethods(['offsetExists','offsetGet','offsetSet','offsetUnset'])
            ->getMock()
        ;
        $response->method('offsetExists')
            ->willReturn(true)
        ;


        $queryResponse
            ->method('getResponse')
            ->willReturn($response)
        ;


        $sl = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock()
        ;

        $this->target = ResultConverter::factory($sl);
        $this->filter = $this->getMockBuilder(AbstractPaginationQuery::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProxyClass','createQuery','getRepositoryName'])
            ->getMock()
        ;
        $this->response = $response;
        $this->queryResponse = $queryResponse;
        $this->sl = $sl;
    }

    public function testConvert()
    {
        $target = $this->target;
        $response = $this->response;
        $filter = $this->filter;
        $sl = $this->sl;

        $repositories = $this->getMockBuilder(RepositoryService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $repository = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['createQueryBuilder'])
            ->getMock()
        ;
        $qb = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $document = $this->getMockBuilder(JobInterface::class)
            ->getMock()
        ;

        $qb->method('hydrate')->willReturn($qb);
        $qb->method('field')->willReturn($qb);
        $qb->method('in')->willReturn($qb);
        $qb->method('getQuery')->willReturn($query);
        $query->method('execute')->willReturn([$document]);

        $sl->method('get')
            ->with('repositories')
            ->willReturn($repositories)
        ;
        $repositories->expects($this->once())
            ->method('get')
            ->with('Some\Repository')
            ->willReturn($repository)
        ;
        $repository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($qb)
        ;

        $filter
            ->expects($this->once())
            ->method('getProxyClass')
            ->willReturn('Solr\Entity\SolrJob')
        ;
        $filter->expects($this->once())
            ->method('getRepositoryName')
            ->willReturn('Some\Repository')
        ;

        $doc = new ResultDocument([
            'id' => 'some-id',
            'title' => 'some-title',
            'dateCreated' => '2016-06-28T08:48:37Z',
            'customField' => 'Some Company'
        ]);
        $response
            ->method('offsetGet')
            ->withConsecutive(['response'],['docs'])
            ->willReturnOnConsecutiveCalls($response,[$doc])
        ;

        $document->method('getId')->willReturn('some-id');
        $document->method('getTitle')->willReturn('some-title');

        $entities = $target->convert($filter,$this->queryResponse);
        $job = $entities[0];

        $this->assertEquals('some-id',$job->getId());
        $this->assertEquals('some-title',$job->getTitle());
    }
}