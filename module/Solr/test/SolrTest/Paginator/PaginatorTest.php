<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */
namespace SolrTest\Paginator;

use Solr\Paginator\Paginator;
use Solr\Paginator\Adapter\SolrAdapter;
use Solr\Facets;

/**
 * @coversDefaultClass \Solr\Paginator\Paginator
 */
class PaginatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__construct()
     * @expectedException \Zend\Paginator\Exception\InvalidArgumentException
     * @expectedExceptionMessage adapter must implement
     */
    public function testConstructorThrowInvalidArgumentException()
    {
        new Paginator('invalid');
    }
    
    /**
     * @covers ::getFacets()
     * @covers ::__construct()
     */
    public function testGetFacets()
    {
        $facets = new Facets();
        
        $adapter = $this->getMockBuilder(SolrAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adapter->expects($this->once())
            ->method('getFacets')
            ->willReturn($facets);
        
        $paginator = new Paginator($adapter);
        $this->assertSame($facets, $paginator->getFacets());
    }
}

