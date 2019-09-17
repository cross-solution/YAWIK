<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Form\Tree;

use PHPUnit\Framework\TestCase;

use Core\Form\Tree\AddItemFieldset;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Tests for \Core\Form\Tree\AddItemFieldset
 *
 * @covers \Core\Form\Tree\AddItemFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 * @group Core.Form.Tree
 */
class AddItemFieldsetTest extends TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait, TestDefaultAttributesTrait;

    private $target = [
        AddItemFieldset::class,
        '@testInitializesItself' => 'setupMock'
    ];

    private $inheritance = [ Fieldset::class, ViewPartialProviderInterface::class, InputFilterProviderInterface::class];

    private $traits = [ViewPartialProviderTrait::class];

    private $attributes = [
        'defaultPartial' => 'core/form/tree-add-item',
    ];

    private function setupMock()
    {
        $mock = $this->getMockBuilder(AddItemFieldset::class)
            ->setMethods(['setObject', 'add'])
             ->disableOriginalConstructor()->getMock();

        $mock->expects($this->once())->method('setObject')->with($this->isInstanceOf(\ArrayObject::class));

        $mock->expects($this->exactly(6))->method('add')
             ->withConsecutive(
                 [['name' => 'id', 'type' => 'Hidden']],
                 [['name' => 'current', 'type' => 'Hidden']],
                 [['name' => 'do', 'type' => 'Hidden']],
                 [['name' => 'name', 'type' => 'Text', 'options' => ['label' => 'Name'], 'attributes' => ['required' => 'required']]],
                 [['name' => 'value', 'type' => 'Text', 'options' => ['label' => 'Value']]],
                 [['name' => 'priority', 'type' => 'Text']]
             );

        return $mock;
    }

    public function testInitializesItself()
    {
        $this->target->init();
    }

    public function testInputFilterSpecifications()
    {
        $expected = [
            'name' => [
                'required' => true,
                'filters' => [
                    [ 'name' => 'StringTrim' ],
                ],
            ],
            'value' => [
                'required' => false,
                'filters' => [
                    [ 'name' => 'StringTrim' ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->target->getInputFilterSpecification());
    }
}
