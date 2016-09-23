<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace SolrTest;

use Solr\Facets;
use ArrayObject;
use SolrDisMaxQuery;
use InvalidArgumentException;

/**
 * @coversDefaultClass \Solr\Facets
 */
class FacetsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Facets
     */
    protected $facets;
    
    /**
     * @var string
     */
    const DEFINITION_NAME = 'firstDefinition';
    
    
    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->facets = new Facets();
        $this->facets->addDefinition(static::DEFINITION_NAME, 'First title', Facets::TYPE_FIELD);
    }

    /**
     * @covers ::addDefinition()
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage invalid type
     */
    public function testAddDefinitionThrowsInvalidArgumentException()
    {
        $this->facets->addDefinition('name', 'title', 'invalid');
    }
    
    /**
     * @param array $facetResult
     * @param int $expectedCount
     * @param mixed $expectedValue
     * @covers ::getIterator()
     * @covers ::addDefinition()
     * @dataProvider facetResultData()
     */
    public function testGetIterator(array $facetResult, $expectedCount, $expectedValue = null)
    {
        $this->facets->setFacetResult(new ArrayObject($facetResult));
        $iterator = $this->facets->getIterator();
        $this->assertInstanceOf(\Iterator::class, $iterator);
        $actual = [];
        
        foreach ($iterator as $key => $value) {
            $actual[$key] = $value;
        }
        
        $this->assertCount($expectedCount, $actual);
        
        if (isset($expectedValue)) {
            $this->assertEquals($expectedValue, $actual);
        }
    }

    /**
     * @param array $facetResult
     * @param int $expectedCount
     * @covers ::count()
     * @dataProvider facetResultData()
     */
    public function testCount(array $facetResult, $expectedCount)
    {
        $this->facets->setFacetResult(new ArrayObject($facetResult));
        $this->assertEquals($expectedCount, $this->facets->count());
    }
    
    /**
     * @param array $facetResult
     * @param int $expectedCount
     * @param mixed $expectedValue
     * @covers ::toArray()
     * @covers ::setFacetResult()
     * @dataProvider facetResultData()
     */
    public function testToArray(array $facetResult, $expectedCount, $expectedValue = null)
    {
        $this->facets->setFacetResult(new ArrayObject($facetResult));
        $actual = $this->facets->toArray();
        $this->assertInternalType('array', $actual);
        $this->assertCount($expectedCount, $actual);
        
        if (isset($expectedValue)) {
            $this->assertEquals($expectedValue, $actual);
        }
    }
    /**
     * @covers ::setFacetResult()
     */
    public function testSetFacetResult()
    {
        $this->assertSame($this->facets, $this->facets->setFacetResult(new ArrayObject()));
    }

    /**
     * @covers ::setParams()
     */
    public function testSetParams()
    {
        $this->assertSame($this->facets, $this->facets->setParams([]));
    }

    /**
     * @param array $params
     * @param int $addFilterQueryInvocations
     * @covers ::setupQuery()
     * @dataProvider setupQueryData()
     */
    public function testSetupQuery(array $params, $addFilterQueryInvocations, callable $addFilterQueryWithCallback = null)
    {
        $this->facets->setParams($params);
        
        $query = $this->getMockBuilder(SolrDisMaxQuery::class)
            ->getMock();
        
        $query->expects($this->once())
            ->method('setFacet')
            ->with($this->equalTo(true));
        
        $query->expects($this->atLeastOnce())
            ->method('addFacetField');
        
        $addFilterQuery = $query->expects($this->exactly($addFilterQueryInvocations))
            ->method('addFilterQuery');
        
        if (isset($addFilterQueryWithCallback)) {
            $addFilterQuery->with($this->callback($addFilterQueryWithCallback));
        }
        
        $this->facets->setupQuery($query);
    }
    
    /**
     * @param string $name
     * @param string $value
     * @param array $params
     * @param bool $active
     * @param bool $invalid
     * @covers ::isValueActive()
     * @covers ::assertValidName()
     * @dataProvider valueActiveData()
     */
    public function testIsValueActive($name, $value, array $params, $active = false, $invalid = false)
    {
        if ($invalid) {
            $this->setExpectedException(InvalidArgumentException::class);
        }
        
        $this->facets->setParams($params);
        $this->assertSame($active, $this->facets->isValueActive($name, $value));
    }
    

    /**
     * @param array $facetResult
     * @param array $params
     * @param array $expected
     * @covers ::getActiveValues()
     * @dataProvider getActiveValuesData()
     */
    public function testGetActiveValues(array $facetResult, array $params, array $expected)
    {
        $this->facets->setFacetResult(new ArrayObject($facetResult));
        $this->facets->setParams($params);
        
        $actual = $this->facets->getActiveValues();
        $this->assertInternalType('array', $actual);
        $this->assertSame($expected, $actual);
    }

    /**
     * @param unknown $name
     * @param unknown $title
     * @covers ::getTitle()
     * @covers ::assertValidName()
     * @dataProvider getTitleData()
     */
    public function testGetTitle($name, $title)
    {
        $this->facets->addDefinition($name, $title);
        $this->assertSame($title, $this->facets->getTitle($name));
    }

    /**
     * @covers ::getTitle()
     * @covers ::assertValidName()
     * @expectedException \InvalidArgumentException
     */
    public function testGetTitleThrowsInvalidArgumentException()
    {
        $this->facets->getTitle('invalid');
    }
    
    /**
     * @return array
     */
    public function facetResultData()
    {
        return [
            'empty' => [[], 0],
            'invalid type' => [['invalid' => []], 0],
            'invalid name' => [[Facets::TYPE_FIELD => ['invalid' => []]], 0],
            'valid with no values' => [[Facets::TYPE_FIELD => [static::DEFINITION_NAME => []]], 1, [static::DEFINITION_NAME => []]],
            'valid with regular values' => [[Facets::TYPE_FIELD => [static::DEFINITION_NAME => ['one' => 1]]], 1, [static::DEFINITION_NAME => ['one' => 1]]],
            'valid with values containing empty key' => [[Facets::TYPE_FIELD => [static::DEFINITION_NAME => ['two' => 2, '' => 8]]], 1, [static::DEFINITION_NAME => ['two' => 2]]],
        ];
    }
    
    /**
     * @return array
     */
    public function setupQueryData()
    {
        return [
            'empty params' => [[], 0],
            'invalid name' => [['invalid' => []], 0],
            'invalid type' => [[static::DEFINITION_NAME => 'invalid'], 0],
            'empty value' => [[static::DEFINITION_NAME => []], 0],
            'regular value' => [[static::DEFINITION_NAME => ['one' => '', 'two' => '']], 1, function ($value) {
                return strpos($value, static::DEFINITION_NAME.':(one OR two)') !== false;
            }],
        ];
    }
    
    /**
     * @return array
     */
    public function valueActiveData()
    {
        return [
            'invalid name' => ['invalid', 'some', [], false, true],
            'empty params' => [static::DEFINITION_NAME, 'some', []],
            'invalid params' => [static::DEFINITION_NAME, 'some', [static::DEFINITION_NAME => 'invalid']],
            'inactive' => [static::DEFINITION_NAME, 'some', [static::DEFINITION_NAME => ['another' => '']]],
            'active' => [static::DEFINITION_NAME, 'some', [static::DEFINITION_NAME => ['some' => '']], true],
        ];
    }
    
    /**
     * @return array
     */
    public function getActiveValuesData()
    {
        $facetResult = [Facets::TYPE_FIELD => [static::DEFINITION_NAME => ['one' => 1, 'two' => 2, 'three' => 3]]];
        
        return [
            'invalid name' => [$facetResult, ['invalid' => []], []],
            'invalid value' => [$facetResult, [static::DEFINITION_NAME => 'invalid'], []],
            'empty' => [$facetResult, [static::DEFINITION_NAME => []], []],
            'single' => [$facetResult, [static::DEFINITION_NAME => ['three' => '']], [static::DEFINITION_NAME => ['three']]],
            'keep definition order' => [$facetResult, [static::DEFINITION_NAME => ['two' => '', 'one' => '']], [static::DEFINITION_NAME => ['one', 'two']]],
        ];
    }
    
    /**
     * @return array
     */
    public function getTitleData()
    {
        return [
            ['first', '1. title'],
            ['second', '2. title'],
            ['third', '3. title'],
            ['first', '1. title (replaced)']
        ];
    }
}

