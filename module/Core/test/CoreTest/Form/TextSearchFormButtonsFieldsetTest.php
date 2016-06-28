<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Form;


use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Tests for \Core\Form\TextSearchFormButtonsFieldset
 * 
 * @covers \Core\Form\TextSearchFormButtonsFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 */
class TextSearchFormButtonsFieldsetTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    /**
     *
     *
     * @var string|\Core\Form\TextSearchForm
     */
    protected $target = [
        'class' => '\Core\Form\TextSearchFormButtonsFieldset',
        'mock' => [
            'testInitializationAddsDefaultButtons' => ['addButton'],
            'testAddButton' => ['add'],
        ],
    ];

    protected $properties = [
        [ 'span',  [ 'value' => 8, 'default' => 12 ]],
    ];

    protected $inheritance = [ '\Zend\Form\Fieldset' ];

    public function testInitializationAddsDefaultButtons()
    {
        $this->target
            ->expects($this->exactly(2))
            ->method('addButton')
            ->withConsecutive(
                ['Search', -1000, 'submit' ],
                ['Clear', -1100, 'reset' ]
            );

        $this->target->init();
    }

    public function buttonValuesProvider()
    {
        return [
            [ ['test', 'LABEL'], 0, 'button', ['name' => 'test', 'label' => 'LABEL', 'class' => 'default'] ],
            [ 'Labeled Button', 0, 'someType', ['name' => 'labeled_button', 'label' => 'Labeled Button', 'class' => 'default']],
            [ 'test', 12, 'submit', ['name' => 'test', 'label' => 'test' ,'class' => 'primary' ] ],
        ];
    }

    /**
     * @dataProvider buttonValuesProvider()
     *
     * @param       $label
     * @param       $priority
     * @param       $type
     * @param array $expect
     */
    public function testAddButton($label, $priority, $type, $expect = [])
    {
        $expectWith = [
            'type' => 'Button',
            'name' => $expect['name'],
            'options' => [
                'label' => $expect['label'],
            ],
            'attributes' => [
                'class' => 'btn btn-' . $expect['class'],
                'type' => $type,
            ],
        ];

        $this->target->expects($this->once())->method('add')->with($expectWith, ['priority' => $priority]);
        $this->target->addButton($label, $priority, $type);
    }
}