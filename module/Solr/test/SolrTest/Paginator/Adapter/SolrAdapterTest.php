<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Paginator\Adapter;


use Solr\Filter\AbstractPaginationQuery;
use Solr\Paginator\Adapter\SolrAdapter;

/**
 * Class SolrAdapterTest
 *
 * @package SolrTest\Paginator\Adapter
 * @covers Solr\Paginator\Adapter\SolrAdapter
 */
class SolrAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Class Under test
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $target;

    /**
     * SolrClient Mock
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $client;

    /**
     * AbstractPaginationQuery Mock
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    protected $responseArray = [
        'response' => [
            'numFound' => 3,
            'docs' => [
                ['index' => 1],
                ['index' => 2],
                ['index' => 3]
            ]
        ]
    ];

    public function setUp()
    {
        $client = $this->getMockBuilder(\SolrClient::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $filter = $this->getMockBuilder(AbstractPaginationQuery::class)
            ->getMock()
        ;

        $this->target = new SolrAdapter($client,$filter,array());
        $this->response = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['getArrayResponse'])
            ->getMock()
        ;
        $this->client = $client;
        $this->filter = $filter;

        $this->response
            ->method('getArrayResponse')
            ->willReturn($this->responseArray)
        ;
    }

    public function testGetItemsAndCount()
    {
        $this->client
            ->expects($this->once())
            ->method('query')
            ->with($this->isInstanceOf('SolrQuery'))
            ->willReturn($this->response)
        ;

        $this->filter
            ->expects($this->once())
            ->method('filter')
            ->willReturn(new \SolrQuery())
        ;

        $retVal = $this->target->getItems(0,10);
        $this->assertEquals($this->responseArray['response']['docs'],$retVal);
        $this->assertEquals(3,$this->target->count());
    }
}