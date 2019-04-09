<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Form\Element;

use PHPUnit\Framework\TestCase;

use Organizations\Form\Element\InviteEmployeeBar;

/**
 * Tests for Organizations\Form\Element\InviteEmployeeBar
 *
 * @covers \Organizations\Form\Element\InviteEmployeeBar
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Form
 * @group Organizations.Form.Element
 */
class InviteEmployeeBarTest extends TestCase
{

    /**
     * @testdox Extends \Zend\Form\Element\Text and implements \Core\Form\HeadscriptProviderInterface, \Core\Form\ViewPartialProviderInterface
     */
    public function testExtendsTextElement()
    {
        $target = new InviteEmployeeBar();

        $this->assertInstanceOf('\Zend\Form\Element\Text', $target);
        $this->assertInstanceOf('\Core\Form\HeadscriptProviderInterface', $target);
        $this->assertInstanceOf('\Core\Form\ViewPartialProviderInterface', $target);
    }

    /**
     * @testdox Has default values for view partial name and headscripts array
     */
    public function testDefaultAttributeValues()
    {
        $target = new InviteEmployeeBar();

        $this->assertAttributeEquals(array('modules/Organizations/js/form.invite-employee.js'), 'headscripts', $target);
        $this->assertAttributeEquals('organizations/form/invite-employee-bar', 'partial', $target);
    }

    /**
     * @testdox Allows setting and getting a view partial name
     */
    public function testSetAndGetViewPartial()
    {
        $this->doSetterGetterTest('ViewPartial', 'testpartialname');
    }

    /**
     * @testdox Allows setting and getting headscripts array
     */
    public function testSetAndGetHeadscripts()
    {
        $this->doSetterGetterTest('Headscripts', array('testscript', 'yetanotherone'));
    }

    private function doSetterGetterTest($method, $value)
    {
        $target = new InviteEmployeeBar();
        $setMethod = "set$method";
        $getMethod = "get$method";

        $this->assertSame($target, $target->$setMethod($value), 'Fluent interface broken!');
        $this->assertEquals($value, $target->$getMethod());
    }
}
