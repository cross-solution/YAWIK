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

use CoreTestUtils\TestCase\AssertInheritanceTrait;
use CoreTestUtils\TestCase\SetterGetterTrait;

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

    protected $inheritance = [ '\Zend\Form\Form' , '\Core\Form\HeadscriptProviderInterface' ];

    protected $properties = [
        [ 'Headscripts', [ 'default' => [ 'Core/js/core.searchform.js' ], 'value' => [ 'some/other/headscripts' ] ] ],
    ];

    public function testAllowsSettingSearchParamsAsJson()
    {
        $arrayValue = [ 'test' => 'works' ];
        $arrayExpect = \Zend\Json\Json::encode($arrayValue);



        $this->assertSame($this->target, $this->target->setSearchParams($arrayValue), 'breaks fluent interface.');
        $this->assertEquals($arrayExpect, $this->target->getAttribute('data-search-params'), 'Setting array failed.');


    }

    public function testInitializesItself()
    {
        /* @var \Core\Form\TextSearchForm|\PHPUnit_Framework_MockObject_MockObject $target */
        $name = 'search';
        $placeholder = 'placeholder';
        $attributes = [
            'data-handle-by' => 'native',
            'method' => 'get',
            'class' => 'form-inline search-form',
        ];
        $add = [
                       'type' => 'Text',
                       'name' => 'text',
                       'options' => [
                           'label' => 'Search',
                           'use_formrow_helper' => false,
                       ],
                       'attributes' => [
                           'class' => 'form-control',
                           'placeholder' => $placeholder,
                       ]
                   ];

        $target = $this->getMock(get_class($this->target), ['add', 'setName', 'setAttributes', 'getOption']);
        $target->expects($this->once())->method('setName')->with($name);
        $target->expects($this->once())->method('setAttributes')->with($attributes);
        $target->expects($this->once())->method('getOption')->with('placeholder')->willReturn($placeholder);
        $target->expects($this->once())->method('add')->with($add);

        $target->init();
    }
}