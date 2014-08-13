<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */


namespace AuthTest\Mapper\MongoDb;

use Auth\Mapper\MongoDb\UserMapper as Mapper;
use Auth\Model\User;

class UserMapperTest extends \PHPUnit_Framework_TestCase
{
    
    public function testMapperImplementsUserMapperInterface()
    {
        $mapper = new Mapper();
        $this->assertInstanceOf('\Auth\Mapper\UserMapperInterface', $mapper);
    } 
    
    private function getTestMapper($returnValue)
    {
        $mapper = new Mapper();
        $mapper->setModelPrototype(new \Auth\Model\User());
        
        $collection = $this->getMockBuilder('\MongoCollection')
        ->disableOriginalConstructor()
        ->setMethods(array('findOne'))
        ->getMock();
        $collection
        ->expects($this->exactly(2))
        ->method('findOne')
        ->will($returnValue);
        
        $mapper->setCollection($collection);
        return $mapper;
    }
    
    public function testFindByEmail()
    {
        $mapper = $this->getTestMapper($this->onConsecutiveCalls(
            array('_id' => new \MongoId('test1'), 'email' => 'test@mail'),
            null
        ));
        $model = $mapper->findByEmail('test1');
        $this->assertInstanceOf('\Auth\Model\User', $model);
        $this->assertEquals('test@mail', $model->email);
        
        $model = $mapper->findByEmail('test2');
        $this->assertNull($model);
    }
    
    public function testFindByProfileIdentifier()
    {
        $mapper = $this->getTestMapper($this->onConsecutiveCalls(
           array('_id' => new \MongoId('test2'), 'email' => 'test2@mail', 'profile' => array('identifier' => 'test2profile')),
           null 
        ));
        
        $model = $mapper->findByProfileIdentifier('test2profile');
        $this->assertNotNull($model);
        $this->assertInstanceOf('\Auth\Model\User', $model);
        $this->assertEquals('test2profile', $model->profile['identifier']);
        
        $model = $mapper->findByProfileIdentifier('test3profile');
        $this->assertNull($model);
    }
    
    public function testSaveWithoutProfile()
    {
        $mapper = new Mapper();
        
        $modelData = array(
            'email' => '',
            'firstName' => '',
            'lastName' => '',
            'displayName' => 'Test User'
            
        );
        $mapper->setCollection(new MongoCollectionMock($modelData));
        
        $model = new User();
        $model->setData($modelData);
        
        
        $mapper->save($model);
        
        $this->assertEmpty($model->profile);
        $this->assertEquals('testId', $model->getId());
    }
    
    public function testSaveWithProfile()
    {
        $modelData = array(
            'email' => '',
            'firstName' => '',
            'lastName' => '',
            'displayName' => 'Test User',
            'profile' => array(
                'identifier' => 'testUser'
            ),
            
        );
         
        $mapper = new Mapper();
        $mapper->setCollection(new MongoCollectionMock($modelData));
        
        
        
        $model = new User();
        $model->setData($modelData);
        
        $mapper->save($model);
        
        $this->assertNotEmpty($model->profile);
        $this->assertEquals('testUser', $model->profile['identifier']);
    }
    
    public function testSaveWithIdDoesNotChangeId()
    {
        $mongoId = new \MongoId('testUser');
        $modelData = array(
            'email' => '',
            'firstName' => '',
            'lastName' => '',
            'displayName' => '',
        );
        $expectedModelData = $modelData;
        $expectedModelData['_id'] = $mongoId;
        $mapper = new Mapper();
        $mapper->setCollection(new MongoCollectionMock($expectedModelData));
        
        $model = new User();
        $model->setData($modelData);
        $model->setId($mongoId);
        $mapper->save($model);

        $this->assertEquals((string) $mongoId, $model->getId());
    }
    
    public function testSaveWithOutIdDoesInsertId()
    {
    
        $modelData = array(
            'email' => '',
            'firstName' => '',
            'lastName' => '',
            'displayName' => '',
        );
        
        $mapper = new Mapper();
        $mapper->setCollection(new MongoCollectionMock($modelData));
    
        $model = new User();
        $model->setData($modelData);
        
        $mapper->save($model);
    
        $this->assertEquals('testId', $model->getId());
    }
}

class MongoCollectionMock extends \MongoCollection
{
    public $expects = array();
    public function __construct(array $expects)
    { 
        $this->expects = $expects;
    }
    
    public function save(&$a, array $options=array())
    {
        if ($a != $this->expects) {
            
            $actual = print_r($a, true);
            $expected=print_r($this->expects, true);
            
            throw new \UnexpectedValueException('DataArray passed to MongoCollection does not meet expectation.'
                . "\n\n" . $actual . "\n\n" . $expected);
        }
        if (!isset($a['_id'])) {
            $a['_id'] = 'testId';
        }
        return true;
    }
}

