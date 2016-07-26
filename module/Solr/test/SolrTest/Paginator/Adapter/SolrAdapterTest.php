<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Paginator\Adapter;


use Solr\Bridge\ResultConverter;
use Solr\Filter\AbstractPaginationQuery;
use Solr\Paginator\Adapter\SolrAdapter;

/**
 * Class SolrAdapterTest
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @author  Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since   0.26
 * @package SolrTest\Paginator\Adapter
 * @covers  Solr\Paginator\Adapter\SolrAdapter
 * @requires extension solr
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
     * Mock of SolrResponse
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * Mock of ResultConverter class
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $converter;

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
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $resultConverter = $this->getMockBuilder(ResultConverter::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->target = new SolrAdapter($client,$filter,$resultConverter,array());
        $this->response = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['getArrayResponse'])
            ->getMock()
        ;
        $this->client = $client;
        $this->filter = $filter;
        $this->converter = $resultConverter;

        $this->response
            ->method('getArrayResponse')
            ->willReturn($this->responseArray)
        ;
    }

    public function testGetItemsAndCount()
    {
        $this->client
            ->expects($this->any())
            ->method('query')
            ->with($this->isInstanceOf('SolrQuery'))
            ->willReturn($this->response)
        ;

        $this->filter
            ->expects($this->any())
            ->method('filter')
            ->willReturn(new \SolrQuery())
        ;

        $this->converter
            ->expects($this->once())
            ->method('convert')
            ->with($this->filter,$this->response)
            ->willReturn([])
        ;

        $retVal = $this->target->getItems(0,5);
        $this->assertEquals([],$retVal);
        $this->assertEquals(3,$this->target->count());
    }

    /**
     * @expectedException \Solr\Exception\ServerException
     * @expectedExceptionMessage Failed to process query
     */
    public function testThrowException()
    {
        $this->client
            ->expects($this->once())
            ->method('query')
            ->with($this->isInstanceOf('SolrQuery'))
            ->willReturnOnConsecutiveCalls($this->throwException(new \Exception()))
        ;

        $this->filter
            ->expects($this->any())
            ->method('filter')
            ->willReturn(new \SolrQuery())
        ;

        $this->target->count();
    }
}