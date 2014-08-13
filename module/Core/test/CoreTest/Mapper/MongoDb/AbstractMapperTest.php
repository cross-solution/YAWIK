<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTest\Mapper\MongoDb;

class AbstractMapperTest extends \PHPUnit_Framework_TestCase
{
    public $mapper;
    
    public function setUp()
    {
        $this->mapper = $this->getMockForAbstractClass('\Core\Mapper\MongoDb\AbstractMapper'); 
    }
    
    public function testAbstractMapperImplementsMapperInterfaceAndExtendsCoreAbstractMapper()
    {
        $this->assertInstanceOf('\Core\Mapper\MongoDb\MapperInterface', $this->mapper);
        $this->assertInstanceOf('\Core\Mapper\MapperInterface', $this->mapper);
        $this->assertInstanceOf('\Core\Mapper\AbstractMapper', $this->mapper); 
    }
    
    public function testCollectionSetterAndGetter()
    {
        $collection = $this->getMock(
            '\MongoCollection',
            array(),
            array(),
            'MockCollection',
            false
        );
        $this->assertSame($this->mapper->setCollection($collection), $this->mapper);
        $this->assertInstanceOf('MockCollection', $this->mapper->getCollection());   
    }
    
    public function createTestDataProvider()
    {
        $id3 = new \MongoId('test');
        return array(
            array(array('id' => 'test'), 'test'),
            array(array('id' => 'test', '_id' => 'test2'), 'test'),
            array(array('_id' => $id3), (string) $id3),
        );
    }
    
    /** @dataProvider createTestDataProvider */
    public function testCreate($data, $expected)
    {
        $modelPrototype = $this->getMockForAbstractClass('\Core\Model\AbstractModel');
        $this->mapper->setModelPrototype($modelPrototype);

        $model = $this->mapper->create($data);
        $this->assertEquals($expected, $model->getId());
         
    }
    
    private function _getCollectionMock()
    {
        $mock = $this->getMock(
            '\MongoCollection',
            array('findOne'),
            array(),
            'CollectionMock',
            false
        );
        return $mock;
    }
    
    public function testFindWithStringId()
    {
        $return = array(
            '_id' => new \MongoId('test'),
        );
        $collection = $this->_getCollectionMock();
        $collection
            ->expects($this->once())
            ->method('findOne')
            ->will($this->returnValue($return));
                
        $modelPrototype = $this->getMockForAbstractClass('\Core\Model\AbstractModel');
        $this->mapper->setModelPrototype($modelPrototype);
        $this->mapper->setCollection($collection);
        
        $model = $this->mapper->find('test');
        $this->assertEquals((string) $return['_id'], $model->getId());
    }
    
    public function testFindWithMongoId()
    {
        $return = array(
            '_id' => new \MongoId('test'),
        );
        $collection = $this->_getCollectionMock();
        $collection
            ->expects($this->once())
            ->method('findOne')
            ->with($return)
            ->will($this->returnValue($return));
        
        $modelPrototype = $this->getMockForAbstractClass('\Core\Model\AbstractModel');
        $this->mapper->setModelPrototype($modelPrototype);
        $this->mapper->setCollection($collection);
        
        $model = $this->mapper->find($return['_id']);
        $this->assertEquals((string) $return['_id'], $model->getId());
    }
}