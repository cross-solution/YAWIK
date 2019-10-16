<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace CoreTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Form\BaseForm;

/**
* @covers \Core\Form\BaseForm
*/
class BaseFormTest extends TestCase
{
    /**
     * @var BaseFrom $target
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = $this->getMockBuilder('Core\Form\BaseForm')
                             ->disableOriginalConstructor()
                             ->setMethods(array('AddButtonsFieldset', 'AddBaseFieldset', 'add'))
                             ->getMock();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\BaseForm', $this->target);
        $this->assertInstanceOf('Zend\Form\Form', $this->target);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage For the Form Core\Form\BaseForm there is no Basefieldset
     */
    public function testInitWithoutBaseForm()
    {
        $target = new BaseForm;
        $target->init();

        $this->assertEquals($target, null);
    }
    
    /**
    * @todo
    */
    /*
    public function testAddBaseFieldsetWithoutBaseFieldsetSet()
    {
    }
    */
}
