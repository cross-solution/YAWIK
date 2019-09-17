<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Form\CustomizableFieldsetInterface;
use Core\Form\CustomizableFieldsetTrait;
use Core\Options\FieldsetCustomizationOptions;
use CoreTestUtils\TestCase\SetupTargetTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Zend\Form\Fieldset;

/**
 * Tests for \Core\Form\CustomizableFieldsetTrait
 *
 * @covers \Core\Form\CustomizableFieldsetTrait
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 */
class CustomizableFieldsetTraitTest extends TestCase
{
    use SetupTargetTrait, TestSetterGetterTrait;

    private $target = [
        CustomizableFieldsetMock::class,
        '@testMergesInputFilterSpecificationsWithDefault' => [
            CustomizableFieldsetMockWithDefaultInputFilter::class
        ]
    ];

    private $properties = [
        ['customizationOptions', ['@value' => FieldsetCustomizationOptions::class, 'default@' => FieldsetCustomizationOptions::class]],
    ];

    public function testCallAddWithNonArrayOrWithoutCustomOptionsCallsParentAdd()
    {
        $opts = new FieldsetCustomizationOptions();

        $this->target->add(['name' => 'test']);

        $this->assertEquals(['name' => 'test'], $this->target->popAddCalledWithArgs()[0]);

        $this->target->setCustomizationOptions($opts);

        $this->target->add(12);

        $this->assertEquals(12, $this->target->popAddCalledWithArgs()[0]);
    }

    public function testCallAddWithoutNameCallsParentAdd()
    {
        $this->target->getCustomizationOptions();
        $spec = ['test' => 'noName'];
        $flags = ['still' => 'noName'];

        $this->target->add($spec, $flags);

        $this->assertEquals([[0 => $spec, 1 => $flags]], $this->target->addCalledWithArgs);
    }

    public function testAddPrefersSpecName()
    {
        $opts = $this->getMockBuilder(FieldsetCustomizationOptions::class)
            ->disableOriginalConstructor()
            ->setMethods(['isEnabled'])
            ->getMock();

        $opts->expects($this->exactly(2))->method('isEnabled')->with('field')->willReturn(false);

        $this->target->setCustomizationOptions($opts);

        $this->target->add(['name' => 'field'], ['name' => 'unused']);
        $this->target->add([], ['name' => 'field']);
    }

    public function testAddMergesSpecifications()
    {
        $opts = new FieldsetCustomizationOptions(
            ['fields' => [
                'test' => [
                    'label' => 'Overridden',
                    'flags' => [
                        'test' => 'AlsoOverridden'
                    ],
                ]
            ]]
        );

        $this->target->setCustomizationOptions($opts);

        $this->target->add(
            ['name' => 'test', 'options' => ['opt' => 'value', 'label' => 'invisible']],
            ['t' => 'y', 'test' => 'behere']
        );

        $this->assertEquals(
            [
                [
                    0 => ['name' => 'test', 'options' => ['opt' => 'value', 'label' => 'Overridden']],
                    1 => [ 't' => 'y', 'test' => 'AlsoOverridden']
                ]
            ],
            $this->target->addCalledWithArgs
        );
    }

    public function testMergesInputFilterSpecifications()
    {
        $opts = new FieldsetCustomizationOptions([
            'fields' => [
                'test' => [
                    'required' => true,
                ]
            ]
        ]);
        $this->target->setCustomizationOptions($opts);
        $actual = $this->target->getInputFilterSpecification();

        $this->assertEquals(
            ['test' => ['required' => true]],
            $actual
        );
    }

    public function testMergesInputFilterSpecificationsWithDefault()
    {
        $opts = new FieldsetCustomizationOptions([
            'fields' => [
                'test' => [
                    'required' => true,
                ]
            ]
        ]);
        $this->target->setCustomizationOptions($opts);
        $actual = $this->target->getInputFilterSpecification();

        $this->assertEquals(
            ['test' => ['required' => true],
              'test2' => ['filters' => []]],
            $actual
        );
    }
}

class CustomizableFieldsetMock extends CustomizableFieldsetParentMock implements CustomizableFieldsetInterface
{
    use CustomizableFieldsetTrait;
}

class CustomizableFieldsetMockWithDefaultInputFilter extends CustomizableFieldsetParentMock implements CustomizableFieldsetInterface
{
    use CustomizableFieldsetTrait;

    protected function getDefaultInputFilterSpecification()
    {
        return [
            'test2' => [
                'filters' => [],
            ],
        ];
    }
}

class CustomizableFieldsetParentMock
{
    public $addCalledWithArgs = [];

    public function popAddCalledWithArgs()
    {
        if (!count($this->addCalledWithArgs)) {
            return false;
        }

        return array_pop($this->addCalledWithArgs);
    }

    public function add($elementOrFieldset, array $flags = [])
    {
        $this->addCalledWithArgs[] = func_get_args();

        return $this;
    }
}
