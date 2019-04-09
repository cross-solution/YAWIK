<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form;

use PHPUnit\Framework\TestCase;

use Jobs\Form\MultipostingSelect;

/**
 * Tests for \Jobs\Form\MultipostingSelect
 *
 * @covers \Jobs\Form\MultipostingSelect
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class MultipostingSelectTest extends TestCase
{

    /**
     * @testdox Extends \Core\Form\Element\Select and Implements \Core\Form\HeadscriptProviderInterface, \Core\Form\ViewPartialProviderInterface
     * @coversNothing
     */
    public function testExtendsSelectElementAndImplementsProperInterfaces()
    {
        $target = new MultipostingSelect();

        $this->assertInstanceOf('\Core\Form\Element\Select', $target, 'Does not extend correct parent class.');
        $this->assertInstanceOf('\Core\Form\HeadscriptProviderInterface', $target, 'Interface HeadscriptProviderInterface not implemented.');
        $this->assertInstanceOf('\Core\Form\ViewPartialProviderInterface', $target, 'Interface ViewPartialProviderInterface not implemented.');
    }

    /**
     * @coversNothing
     */
    public function testProvidesDefaultAttributeValues()
    {
        $target = new MultipostingSelect();

        $this->assertAttributeSame('jobs/form/multiposting-select', 'partial', $target, 'Wrong default value of attribute "partial"');
        $this->assertAttributeSame(array('modules/Jobs/js/form.multiposting-select.js'), 'headscripts', $target, 'Wrong default value of attribute "headscripts"');
    }

    public function provideSetterAndGetterTestData()
    {
        return array(
            array('ViewPartial', 'test/partial/name'),
            array('Headscripts', array('test/script', 'yet another test script'))
        );
    }

    /**
     * @testdox Allows setting and getting view partial name and injectable headscripts.
     * @dataProvider provideSetterAndGetterTestData
     *
     * @param string $method
     * @param mixed $value
     */
    public function testSetterAndGetter($method, $value)
    {
        $target = new MultipostingSelect();
        $setter = "set$method";
        $getter = "get$method";

        $this->assertSame($target, $target->$setter($value), 'Fluent interface broken.');
        $this->assertEquals($value, $target->$getter());
    }
}
