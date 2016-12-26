<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Form;

use Core\Form\ViewPartialProviderInterface;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Form\SearchForm;

/**
 * Tests for \Cv\Form\SearchForm
 * 
 * @covers \Cv\Form\SearchForm
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Test
 */
class SearchFormTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait, TestSetterGetterTrait;

    private $target = [
        SearchForm::class,
        '@testInitializesItself' => [
            'mock' => [
                'setName' => ['with' => 'cv-list-filter', 'count' => 1],
                'setAttributes' => ['with' => [['id' => 'cv-list-filter', 'data-handle-by' => 'native']], 'count' => 1],
                'add' => ['with' => [['type' => 'Cv/SearchFormFieldset', 'options' => ['use_as_base_fieldset' => false]]], 'count' => 1],
            ],
        ],
    ];

    private $inheritance = [ '\Zend\Form\Form', ViewPartialProviderInterface::class ];

    private $attributes = [
        'fieldset' => 'Cv/SearchFormFieldset',
    ];

    private $properties = [
        ['viewPartial', ['value' => 'another/partial', 'default' => 'cv/form/search.phtml', 'setter_value' => null]]
    ];

    public function testInitializesItself()
    {
        $this->target->init();
    }
}