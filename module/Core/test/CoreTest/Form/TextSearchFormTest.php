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

use Core\Form\TextSearchFormButtonsFieldset;
use Core\Form\TextSearchFormFieldset;
use CoreTestUtils\TestCase\AssertInheritanceTrait;
use CoreTestUtils\TestCase\SetterGetterTrait;
use Zend\Form\Fieldset;

/**
 * Tests for \Core\Form\TextSearchForm
 * 
 * @covers \Core\Form\TextSearchForm
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 */
class TextSearchFormTest extends \PHPUnit_Framework_TestCase
{
    use AssertInheritanceTrait, SetterGetterTrait;

    /**
     *
     *
     * @var string|\Core\Form\TextSearchForm
     */
    protected $target = '\Core\Form\TextSearchForm';

    protected $inheritance = [ '\Zend\Form\Form'  ];

    public function propertiesProvider()
    {
        return [
            [ 'options', [
                'value' => [],
                'ignore_getter' => true,
                'setter_assert' => 'assertSetOptionsResult',
                'setter_value' => 'empty',
            ]],
            [ 'options', [
                'value' => ['elements_fieldset' => 'Test'],
                'ignore_getter' => true,
                'setter_assert' => 'assertSetOptionsResult',
                'setter_value' => 'elements',
            ]],
            [ 'options', [
                'value' => ['buttons_fieldset' => 'Test'],
                'ignore_getter' => true,
                'setter_assert' => 'assertSetOptionsResult',
                'setter_value' => 'buttons',
            ]],
            [ 'options', [
                'value' => ['name' => 'test'],
                'ignore_getter' => true,
                'setter_assert' => 'assertSetNameViaOptions',
                'setter_value' => 'test'
            ]],
        ];
    }

    public function assertSetOptionsResult($name, $returned, $expect)
    {
        $this->assertSame($returned, $this->target);

        $expectOptions = [];
        $expectElementsFieldset = 'Core/TextSearch/Elements';
        $expectButtonsFieldset  = 'Core/TextSearch/Buttons';


        if ('elements' == $expect) {
            $expectOptions = [ 'elements_fieldset' => 'Test' ];
            $expectElementsFieldset = 'Test';
        } else if ('buttons' == $expect) {
            $expectOptions = [ 'buttons_fieldset' => 'Test' ];
            $expectButtonsFieldset = 'Test';
        }

        $this->assertAttributeEquals($expectOptions, 'options', $this->target);
        $this->assertAttributeEquals($expectElementsFieldset, 'elementsFieldset', $this->target);
        $this->assertAttributeEquals($expectButtonsFieldset, 'buttonsFieldset', $this->target);

    }

    public function assertSetNameViaOptions($name, $returned, $expect)
    {
        $this->assertEquals($this->target->getName(), $expect);
    }

    public function testAllowsSettingSearchParamsAsJson()
    {
        $arrayValue = [ 'test' => 'works' ];
        $arrayExpect = \Zend\Json\Json::encode($arrayValue);

        $traversableValue = new \ArrayIterator($arrayValue);
        $traversableExpect = \Zend\Json\Json::encode($arrayValue);


        $this->assertSame($this->target, $this->target->setSearchParams($arrayValue), 'breaks fluent interface.');
        $this->assertEquals($arrayExpect, $this->target->getAttribute('data-search-params'), 'Setting array failed.');

        $this->target->setSearchParams($traversableValue);
        $this->assertEquals($traversableExpect, $this->target->getAttribute('data-search-params'), 'Setting \Traversable failed.');


    }

    public function testInitializesItselfSetsDefaultAttributes()
    {
        $attributes = [
            'class' => 'form-inline search-form',
            'data-handle-by' => 'script',
            'method' => 'get',
        ];
        $target = $this->getMock(get_class($this->target), ['add', 'setAttributes']);
        $target->expects($this->once())->method('setAttributes')->with($attributes);

        $target->init();
    }

    public function testInitializesItselfSetsDefaultName()
    {
        $target = $this->getMock(get_class($this->target), ['add', 'setName']);
        $target->expects($this->once())->method('setName')->with('search');

        $target->init();
    }

    public function testInitializesItselfUsesName()
    {
        $target = $this->getMock(get_class($this->target), ['add', 'setName']);
        $target->expects($this->never())->method('setName');
        $target->setAttribute('name', 'test');
        $target->init();
    }

    public function testInitializesItselfWithNonObjectFieldsets()
    {
        $target = $this->getMock(get_class($this->target), ['add']);

        $addElements = [
            'type' => 'Core/TextSearch/Elements',
            'options' => []
        ];

        $addButtons = [
            'type' => 'Core/TextSearch/Buttons',
        ];

        $target->expects($this->exactly(2))
            ->method('add')->withConsecutive([$addElements], [$addButtons]);

        $target->init();
    }

    public function testInitializesItselfWithObjectFieldset()
    {
        $target  = $this->getMock(get_class($this->target), ['add']);
        $elements = new TextSearchFormFieldset();
        $buttons  = new TextSearchFormButtonsFieldset();

        $options = [
            'elements_fieldset' => $elements,
            'buttons_fieldset' => $buttons,
        ];

        $target
            ->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive([$elements], [$buttons]);

        $target->setOptions($options);
        $target->init();
    }

    public function invalidNamedElementsProvider()
    {
        $fs = new TextSearchFormButtonsFieldset();
        $fs->setName('invalid');

        return [
            [ 'invalidnamed', [] ],
            [ ['type' => 'Core/TextSearch/Elements'], ['name' => 'invalid']],
            [ ['type' => 'Core/TextSearch/Elements', 'name' => 'invalid'], []],
            [ new TextSearchFormFieldset(), []],
            [ $fs, []]
        ];
    }

    /**
     * @dataProvider invalidNamedElementsProvider
     *
     * @param $element
     * @param $flags
     */
    public function testAddElementsWithInvalidNameThrowsException($element, $flags)
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid named element');

        $this->target->add($element, $flags);
    }

    public function testAddInvalidElementsThrowsException()
    {
        $this->setExpectedException('\UnexpectedValueException', 'extends from TextSearchForm');

        $this->target->add(new Fieldset(), ['name' => 'elements']);
    }

    public function testAddValidElements()
    {
        $elements = new TextSearchFormFieldset();

        $this->target->add($elements, ['name' => 'elements']);

        $actual = $this->target->get('elements');

            $this->assertSame($elements, $actual);
    }

    public function testGetElementsAndGetButtonsProxyToGet()
    {
        $elements = new TextSearchFormFieldset();
        $buttons = new TextSearchFormButtonsFieldset();
        $target = $this->getMock(get_class($this->target), ['get']);

        $target
            ->expects($this->exactly(4))
            ->method('get')
            ->withConsecutive(['elements'], ['buttons'], ['elements'], ['buttons'])
            ->will($this->onConsecutiveCalls($elements, $buttons, $elements, $buttons));
        $target->add($elements, ['name' => 'elements']);
        $target->add($buttons, ['name' => 'buttons']);

        $target->getElements();
        $target->getButtons();
    }
}