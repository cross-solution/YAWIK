<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Bridge;

use Solr\Bridge\ResultConverter;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Repository\RepositoryService;
use Core\Repository\AbstractRepository;
use Solr\Filter\AbstractPaginationQuery;
use Doctrine\MongoDB\Query\Builder as QueryBuilder;
use Core\Entity\AbstractIdentifiableEntity;
use ArrayObject;

/**
 * Class ResultConverterTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.26
 * @covers Solr\Bridge\ResultConverter
 * @package SolrTest\Bridge
 * @coversDefaultClass \Solr\Bridge\ResultConverter
 */
class ResultConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::factory
     * @covers ::__construct
     */
    public function testFactory()
    {
        $repositories = $this->getMockBuilder(RepositoryService::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with($this->equalTo('repositories'))
            ->willReturn($repositories);
        
        $resultConverter = ResultConverter::factory($serviceLocator);
        $this->assertInstanceOf(ResultConverter::class, $resultConverter);
        
        return [$resultConverter, $repositories];
    }
    
    /**
     * @param array $data
     * @covers ::convert
     * @depends testFactory
     * @dataProvider invalidResponseData
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage invalid response
     */
    public function testConvertThrowsExceptionOnInvalidResponseData($responseData, array $data)
    {
        list ($resultConverter) = $data;
        
        $filter = $this->getMockBuilder(AbstractPaginationQuery::class)
            ->getMock();
        
        $resultConverter->convert($filter, new ArrayObject($responseData));
    }
    
    /**
     * @param array $data
     * @covers ::convert
     * @depends testFactory
     */
    public function testConvert(array $data)
    {
        list ($resultConverter, $repositories) = $data;
        
        $doc = new ArrayObject([
            'id' => 'someId'
        ], ArrayObject::ARRAY_AS_PROPS);
        $invalidDoc = new ArrayObject([
            'id' => 'invalidId'
        ], ArrayObject::ARRAY_AS_PROPS);
        $entity = $this->getMockBuilder(AbstractIdentifiableEntity::class)
            ->setMethods(null)
            ->getMock();
        $entity->setId($doc->id);
        $proxy = new ArrayObject(['proxy']);
        $repositoryName = 'someRepository';
        $response = new ArrayObject([
            'response' => [
                'docs' => [
                    $doc,
                    $invalidDoc
                ]
            ]
        ]);
        
        $filter = $this->getMockBuilder(AbstractPaginationQuery::class)
            ->getMock();
        $filter->expects($this->once())
            ->method('proxyFactory')
            ->with($this->identicalTo($entity), $this->identicalTo($doc))
            ->willReturn($proxy);
        
        $filter->expects($this->once())
            ->method('getRepositoryName')
            ->willReturn($repositoryName);
        
        $query = $this->getMockBuilder(\Doctrine\MongoDB\Query\Query::class)
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())
            ->method('execute')
            ->willReturn([$entity]);
            
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $queryBuilder->expects($this->once())
            ->method('field')
            ->with($this->equalTo('id'))
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('in')
            ->with($this->equalTo([$doc->id, $invalidDoc->id]))
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')->willReturn($query);
        
        $repository = $this->getMockBuilder(AbstractRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
            
        $repositories->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($repositoryName))
            ->willReturn($repository);
        
        $proxies = $resultConverter->convert($filter, $response);
        $this->assertInternalType('array', $proxies);
        $this->assertCount(1, $proxies);
        $this->assertSame($proxy, reset($proxies));
    }
    
    /**
     * @return array
     */
    public function invalidResponseData()
    {
        return [
            [[]],
            [['response' => null]],
            [['response' => ['docs' => 'non-array']]],
        ];
    }
}