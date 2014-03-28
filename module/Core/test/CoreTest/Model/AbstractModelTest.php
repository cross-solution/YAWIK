<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace CoreTest\Model;

class AbstractModelTest extends \PHPUnit_Framework_TestCase
{
    private $model;
    
    public function setUp()
    {
        $this->model = $this->getMockForAbstractClass('\Core\Model\AbstractModel');
    }
    
    public function testIdGetterAndSetter()
    {
    
        $ref = $this->model->setId('test');
        $this->assertSame($ref, $this->model);
        $this->assertEquals('test', $this->model->getId());
    }
    
    /** 
     * @expectedException \Core\Model\Exception\OutOfBoundsException 
     * @expectedExceptionMessage __invalid_property__' is not a valid property 
     */ 
    public function testSetDataThrowsExceptionIfDataContainsInvalidPropertyName()
    {
        
        $this->model->setData(array(
            '__invalid_property__' => 'value'
        ));
    }
    
    public function testSettingAndGettingPropertiesThroughMagicMethodWorks()
    {
        
        $this->model->id = 'test';
        $this->assertEquals('test', $this->model->id);
    }
    /**
     * @expectedException \Core\Model\Exception\OutOfBoundsException
     * @expectedExceptionMessage invalid_property' is not a valid property
     */
    public function testSettingAnInvalidPropertyThrowsException()
    {
        $this->model->invalid_property = 'test';
    }
    
    /**
     * @expectedException \Core\Model\Exception\OutOfBoundsException
     * @expectedExceptionMessage 'invalid_property' is not a valid property
     */
    public function testGettingAnInvalidPropertyThrowsException()
    {
        $this->model->id='validTest';
        $test = $this->model->invalid_property;
    }
    
    public function issetTestProvider()
    {
        return array(
            array(null, false),
            array('', false),
            array('test', true),
            array(array(), false),
            array(array('some' => 'test'), true),
            array(true, true), array(false,true),
            array(new \stdClass(), true),
        );
    } 
    
    /** @dataProvider issetTestProvider */
    public function testIssetReturnsExpectedResults($value, $expect)
    {
        $this->model->id = $value;
        $this->assertTrue($expect === isset($this->model->id));
           
    }
    
    public function testIssetWithInvalidPropertyReturnsFalse()
    {
        $this->assertFalse(isset($this->model->__invalidProperty));
    }
}