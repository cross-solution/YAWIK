<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace CoreTest\Mapper\MongoDb\Service;

use Core\Mapper\MongoDb\Service\MongoDbFactory as Factory;
use Zend\ServiceManager\ServiceManager;

class MongoDbFactoryTest extends \PHPUnit_Framework_TestCase
{
    
    public function testFactoryReturnsMongoDbInstance()
    {
        $sm = new ServiceManager();
        $sm->setFactory('Config', function($sm) {
            return array(
                'database' => array(
                    'connection' => 'testConnectionString',
                    'databaseName' => 'testDatabaseName',
                )
            );
        });
        
        $f = new Factory();
        $f->setMongoClient(new MongoClientMock());
        
        $test = $f->createService($sm);
        
        $this->assertEquals('testDatabaseName', $test);
    }
}

class MongoClientMock extends \MongoClient
{
    
    public function __construct()
    { }
    
    public function __get($name)
    {
        return $name;
    }
}