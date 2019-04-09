<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Options;

use PHPUnit\Framework\TestCase;

use Core\Options\FieldsetCustomizationOptions;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Zend\Stdlib\AbstractOptions;

/**
 * Tests for \Core\Options\FieldsetCustomizationOptions
 *
 * @covers \Core\Options\FieldsetCustomizationOptions
 * @coversDefaultClass \Core\Options\FieldsetCustomizationOptions
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Options
 */
class FieldsetCustomizationOptionsTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    /**
     *
     *
     * @var array|FieldsetCustomizationOptions|Fcot_Mock
     */
    private $target = [
        FieldsetCustomizationOptions::class,
        '@testGetFieldOptions'            => 'getPreparedTarget',
        '@testGetFieldFlags'              => 'getPreparedTarget',
        '@testGetFieldInputSpecification' => 'getPreparedTarget',
        '@testCopyArrayValues' => Fcot_Mock::class,
    ];

    private $inheritance = [ AbstractOptions::class ];

    public function propertiesProvider()
    {
        $fields = [
            'field1' => [],
            'field2' => [],
            'field3' => [
                'enabled' => false,
            ],
            'field4' => [
                'enabled' => true,
            ],
        ];
        return [
            ['fields', ['value' => $fields]],

            /* getFieldNames */
            ['fields', [
                'value' => $fields,
                'expect' => array_keys($fields),
                'getter_method' => 'getFieldNames'
            ]],

            /* hasFields */
            ['fields', [
                'value' => $fields,
                'expect' => true,
                'getter_method' => 'hasField',
                'getter_args' => ['field1'],
            ]],
            ['fields', [
                'value' => $fields,
                'expect' => false,
                'getter_method' => 'hasField',
                'getter_args' => ['inexistent'],
            ]],

            /* isEnabled */
            ['fields', [
                'value' => $fields,
                'expect' => true,
                'getter_method' => 'isEnabled',
                'getter_args' => ['field1']
            ]],
            ['fields', [
                'value' => $fields,
                'expect' => false,
                'getter_method' => 'isEnabled',
                'getter_args' => ['field3']
            ]],
            ['fields', [
                'value' => $fields,
                'expect' => true,
                'getter_method' => 'isEnabled',
                'getter_args' => ['field4']
            ]],

            /* empty field options /flags/input specs*/
            ['fields', [
                'value' => $fields,
                'expect' => [],
                'getter_method' => 'getFieldOptions',
                'getter_args' => ['inexistent'],
            ]],
            ['fields', [
                'value' => $fields,
                'expect' => [],
                'getter_method' => 'getFieldFlags',
                'getter_args' => ['inexistent'],
            ]],
            ['fields', [
                'value' => $fields,
                'expect' => [],
                'getter_method' => 'getFieldInputSpecification',
                'getter_args' => ['inexistent'],
            ]],
        ];
    }

    private function getPreparedTarget()
    {
        return new FieldsetCustomizationOptions([
           'fields' => [
               'field' => [
                   'attributes' => ['attr' => 'attrVal'],
                   'options' => ['opt1' => 'optVal' ],
                   'label' => 'label',
                   'required' => true,
                   'type' => 'ElementType',
                   'priority' => 12,
                   'flags' => ['priority' => 10],
                   'input_filter' => ['test' => 'works'],
               ],
           ],
        ]);
    }

    /**
     * @covers ::getFieldOptions
     */
    public function testGetFieldOptions()
    {
        $expect = [
            'attributes' => ['attr' => 'attrVal', 'required' => 'required'],
            'options' => ['opt1' => 'optVal', 'label' => 'label'],
            'type' => 'ElementType',
        ];

        $this->assertEquals($expect, $this->target->getFieldOptions('field'));
    }

    /**
     * @covers ::getFieldFlags
     */
    public function testGetFieldFlags()
    {
        $expect = [
            'priority' => 12
        ];

        $this->assertEquals($expect, $this->target->getFieldFlags('field'));
    }

    /**
     * @covers ::getFieldInputSpecification
     */
    public function testGetFieldInputSpecification()
    {
        $expect = [
            'test' => 'works',
            'required' => true,
        ];

        $this->assertEquals($expect, $this->target->getFieldInputSpecification('field'));
    }

    public function testCopyArrayValues()
    {
        $source = [
            'required' => false,
        ];

        $keys = [
            'required' => ['if' => true],
        ];

        $this->assertEmpty($this->target->copyArrayValues($source, $keys));
    }
}

class Fcot_Mock extends FieldsetCustomizationOptions
{
    public function copyArrayValues(array $source, array $keys)
    {
        return parent::copyArrayValues($source, $keys);
    }
}
