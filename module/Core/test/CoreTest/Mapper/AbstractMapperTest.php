<?php
/**
 * Cross Applicant Management - Unit Tests
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace CoreTest\Mapper;

class AbstractMapperTest extends \PHPUnit_Framework_TestCase
{
    
    public function testMapperImplementsMapperInterface()
    {
        $mock = $this->getMockForAbstractClass('\Core\Mapper\AbstractMapper');
        $this->assertInstanceOf('\Core\Mapper\MapperInterface', $mock);
    }
    
    public function testSetModelPrototype()
    {
        $mock = $this->getMockForAbstractClass('\Core\Mapper\AbstractMapper');
        $prototype = $this->getMockForAbstractClass('\Core\Model\AbstractModel');
        $mock->setModelPrototype($prototype);
        $this->assertInstanceOf(get_class($prototype), $mock->create());
    }
    
    public function testCreateReturnsClonedPrototype()
    {
        $mock = $this->getMockForAbstractClass('\Core\Mapper\AbstractMapper');
        $prototype = $this->getMockForAbstractClass('\Core\Model\AbstractModel');
        $mock->setModelPrototype($prototype);
        $this->assertNotSame($prototype, $mock->create());

    }
}