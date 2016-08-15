<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Bridge;

use Jobs\Entity\Job;
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
            ->setMethods(['convertCustomField','getEntityClass','createQuery','getPropertiesMap'])
            ->getMock()
        ;
        $this->response = $response;
        $this->queryResponse = $queryResponse;
    }

    /**
     * @dataProvider getTestValidateDate
     */
    public function testValidateDate($expectConverted,$value)
    {
        $target = $this->target;

        $result = $target->validateDate($value);

        if($expectConverted){
            $this->assertInstanceOf(
                \DateTime::class,
                $result,
                '::validateDate() should convert "'.$value.'" into DateTime object'
            );
        }else{
            $this->assertEquals(
                $value,
                $result,
                '::validateDate() should return original value if passed argument is not in date time format string'
            );
        }
    }

    public function getTestValidateDate()
    {
        return [
            [true,'2016-06-28T08:48:37Z'],
            [false,'test']
        ];
    }

    public function testConvert()
    {
        $target = $this->target;
        $response = $this->response;
        $filter = $this->filter;

        $filter
            ->expects($this->once())
            ->method('convertCustomField')
            ->with($this->isInstanceOf(Job::class),'Some Company')
        ;
        $filter
            ->expects($this->once())
            ->method('getPropertiesMap')
            ->willReturn(['customField' => 'convertCustomField'])
        ;
        $filter
            ->expects($this->once())
            ->method('getEntityClass')
            ->willReturn('Jobs\Entity\Job')
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

        $entities = $target->convert($filter,$this->queryResponse);
        $job = $entities[0];

        $this->assertEquals('some-id',$job->getId());
        $this->assertEquals('some-title',$job->getTitle());
        $this->assertInstanceOf(\DateTime::class,$job->getDateCreated());
        $this->assertEquals($doc->dateCreated,$job->getDateCreated()->format(Manager::SOLR_DATE_FORMAT));
    }
}