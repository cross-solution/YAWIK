<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace AuthTest\Mapper\MongoDb\Service;

use Auth\Mapper\MongoDb\Service\UserMapperFactory as Factory;

class UserMapperFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $_sm;
    
    public function setup()
    {
        $sm = new \Zend\ServiceManager\ServiceManager();
        
        $sm->setService('MongoDb', new MongoDbMock());
        $this->_sm = $sm;
    }
    
    public function tearDown()
    {
        $this->_sm = null;
    }
    
    public function testFactoryReturnsInstanceOfUserMapper()
    {
        $factory = new Factory();
        
        $mapper = $factory->createService($this->_sm);
        
        $this->assertInstanceOf('\Auth\Mapper\MongoDb\UserMapper', $mapper);
    }
    
    public function testFactoryInjectsNeededDependencies()
    {
        $factory = new Factory();
        
        $mapper = $factory->createService($this->_sm);
        
        $this->assertInstanceOf('\AuthTest\Mapper\MongoDb\Service\MongoCollectionMock', $mapper->getCollection());
        $this->assertInstanceOf('\Auth\Model\User', $mapper->create());
    }
}

class MongoDbMock extends \MongoDb {
    
    public function __construct()
    { }
    
    public function __get($name)
    {
        if ('users' != $name) {
            throw new \UnexpectedValueException('Property "' . $name . '" was not expected to be accessed.');
        }
        
        return new MongoCollectionMock();
    }
}

class MongoCollectionMock extends \MongoCollection
{ 
    public function __construct()
    { }
}