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
use CoreTestUtils\TestCase\SetterGetterTrait;
use Zend\Form\Element\Text;

/**
 * Tests for \Core\Form\TextSearchFormFieldset
 * 
 * @covers \Core\Form\TextSearchFormFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 */
class TextSearchFormFieldsetTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, SetterGetterTrait;

    /**
     *
     *
     * @var string|\Core\Form\TextSearchForm
     */
    protected $target = [
        'class' => '\Core\Form\TextSearchFormFieldset',
        'mock' => [
            'testPassingTextElementOptionsWithNoTextElementSet' => ['has', 'get'],
            'testPassingTextElementOptionsWithTextElementSet' => ['has', 'get'],
            'testAddsTextElementOnInitialization' => ['add'],
        ]
    ];

    protected $inheritance = [ '\Zend\Form\Fieldset'  ];

    public function propertiesProvider()
    {
        return [
            [ 'buttonElement', 'test' ],
            [ 'buttonElement', [
                'ignore_setter' => true,
                'value' => 'text',
                'post' => ['assertEquals', [ 'text', ['->getOption', ['button_element']], 'Failed']],
            ]],
            [ 'columnMap', [ 'value' => [1,2,3]] ],
            [ 'columnMap', [
                'value' => ['text' => 5 ],
                'ignore_setter' => true,
                'pre' => 'populateElementsForColumnMapTest',
                'post' => ['assertEquals', [ ['text' => 5], ['->getOption', ['column_map']]]],
            ]]

        ];
    }

    public function populateElementsForColumnMapTest()
    {
        $text = new Text();
        $text->setName('text');
        $text->setOption('span', 5);

        $dummy = new Text();
        $dummy->setName('dummy');

        $this->target->add($text);
        $this->target->add($dummy);
    }

    public function testPassingTextElementOptionsWithNoTextElementSet()
    {
        $this->target->expects($this->once())->method('has')->with('text')->willReturn(false);
        $this->target->expects($this->never())->method('get');
        $this->target->setOptions([
                                'text_placeholder' => 'placeholder',
                                'text_span' => 10,
                                'text_label' => 'Test'
                            ]);

    }

    public function testPassingTextElementOptionsWithTextElementSet()
    {
        $placeholder = 'placeholder';
        $span = 8;
        $label = 'Test';

        $text = $this->getMock('\Zend\Form\Element\Text', ['setAttribute', 'setOption', 'setLabel']);
        $text->expects($this->once())->method('setAttribute')->with('placeholder', $placeholder);
        $text->expects($this->once())->method('setOption')->with('span', $span);
        $text->expects($this->once())->method('setLabel')->with($label);


        $this->target->expects($this->once())->method('has')->with('text')->willReturn(true);
        $this->target->expects($this->once())->method('get')->with('text')->willReturn($text);

        $this->target->setOptions([
                                'text_placeholder' => $placeholder,
                                'text_span' => $span,
                                'text_label' => $label
                            ]);
    }


    public function testAddsTextElementOnInitialization()
    {
        $label = 'Test';
        $placeholder = 'Placeholder';
        $span = 8;

        $expect1 =
            [
                'type' => 'Text',
                'name' => 'text',
                'options' => [
                    'label' => 'Search',
                    'span' => 12,
                ],
                'attributes' => [
                    'placeholder' => 'Search query',
                ],
            ];

        $expect2 =
            [
                'type' => 'Text',
                'name' => 'text',
                'options' => [
                    'label' => $label,
                    'span' => $span,
                ],
                'attributes' => [
                    'placeholder' => $placeholder,
                ],
            ];



        $this->target->expects($this->exactly(2))->method('add')
            ->withConsecutive(
                [ $expect1 ],
                [ $expect2 ]
            );

        $this->target->init();

        $this->target->setOptions([
                                      'text_label' => $label,
                                      'text_placeholder'=> $placeholder,
                                      'text_span' => $span
        ]);

        $this->target->init();

    }

    public function testAddElementViaArraySpec()
    {
        $this->target->add([
                               'type' => 'Text',
                               'name' => 'Test',

                           ]);

        $element = $this->target->get('Test');
        $this->assertContains('form-control', $element->getAttribute('class'));

        $this->target->add([
                               'type' => 'Text',
                               'name' => 'Test2',
                               'options' => [
                                'is_button_element' => true,
                                ]
                           ]);

        $this->assertEquals('Test2', $this->target->getButtonElement());
    }

    public function testAddElementViaObject()
    {
        $test = new Text('test');

        $this->target->add($test);
        $this->assertContains('form-control', $test->getAttribute('class'));

        $test2 = new Text('test2', ['is_button_element' => true]);

        $this->target->add($test2);
        $this->assertEquals('test2', $this->target->getButtonElement());
    }

}