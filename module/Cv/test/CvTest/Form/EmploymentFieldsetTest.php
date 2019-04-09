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

use PHPUnit\Framework\TestCase;

use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use Cv\Entity\Employment;
use Cv\Form\EmploymentFieldset;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Tests for \Cv\Form\EmploymentFieldset
 *
 * @covers \Cv\Form\EmploymentFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 */
class EmploymentFieldsetTest extends TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait, TestDefaultAttributesTrait;

    /**
     *
     *
     * @var array|EmploymentFieldset|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        EmploymentFieldset::class,
        '@testInitializesItself' => [
            'mock' => [
                'add',
                'setName' => ['with' => 'employment', 'count' => 1, 'return' => '__self__'],
                'setHydrator' => ['@with' => ['isInstanceOf', EntityHydrator::class ], 'count' => 1, 'return' => '__self__'],
                'setObject' => ['@with' => ['isInstanceOf', Employment::class ], 'count' => 1, 'return' => '__self__'],
            ],
            'args' => false,
        ],
        '@testPopulateValues' => [
            'mock' => [ 'get' ],
        ],
    ];

    private $inheritance = [ Fieldset::class, InputFilterProviderInterface::class, ViewPartialProviderInterface::class ];

    private $traits = [ ViewPartialProviderTrait::class ];

    private $attributes = [
        'defaultPartial' => 'cv/form/employment',
    ];

    public function testInitializesItself()
    {
        $add = [
            [
                'type' => 'Core/Datepicker',
                'name' => 'startDate',
                'options' => [
                    'label' => 'Start date',
                ]
            ],
            [
                 'type' => 'Core/Datepicker',
                 'name' => 'endDate',
                 'options' => [
                     'label' => 'End date'
                 ],
            ],
             [
                 'type' => 'checkbox',
                 'name' => 'currentIndicator',
                 'options' => [
                     'label' => 'ongoing'
                 ]
             ],
             [
                 'name' => 'organizationName',
                 'options' => [
                     'label' => 'Company Name'],
                 'attributes' => [
                     'title' =>  'please enter the name of the company'
                 ],
             ],
             [
                 'name' => 'description',
                 'type' => 'Zend\Form\Element\Textarea',
                 'options' => [
                     'label' => 'Description',
                 ],
                 'attributes' => [
                     'title' => 'please describe your position',
                 ],
             ],
        ];

        $addArgValidator = function ($arg) use ($add) {
            static $count = 0;

            /* PPHUnit calls this callback again after all invokations are made
             * I don't know why, but therefor the need to check if $count is greater that 7
             */
            return 4 < $count || $arg === $add[$count++];
        };

        $this->target
            ->expects($this->exactly(count($add)))
            ->method('add')
            ->with($this->callback($addArgValidator))
            ->will($this->returnSelf())
        ;

        $this->target->init();
    }

    public function testGetInputFilterSpecification()
    {
        $this->assertEquals(['type' => \Cv\Form\InputFilter\Employment::class], $this->target->getInputFilterSpecification());
    }


    public function populateValuesDataProvider()
    {
        return [
            [ [ ], false ],
            [ ['currentIndicator'=>true], false ],
            [ ['currentIndicator' => false, 'endDate' => 'endDate' ], false ],
            [ ['endDate' => 'endDate'], false ],
            [ ['currentIndicator' => true, 'endDate' => 'endDate'], true ],
        ];
    }

    /**
     * @dataProvider populateValuesDataProvider
     *
     * @param $data
     * @param $expectGet
     */
    public function testPopulateValues($data, $expectGet)
    {
        if ($expectGet) {
            $element = $this
                ->getMockBuilder(Element::class)
                ->disableOriginalConstructor()
                ->setMethods(['setOption'])
                ->getMock()
            ;

            $element
                ->expects($this->once())
                ->method('setOption')
                ->with('rowClass', 'hidden')
            ;

            $this->target
                ->expects($this->once())
                ->method('get')
                ->with('endDate')
                ->willReturn($element)
            ;
        } else {
            $this->target->expects($this->never())->method('get');
        }

        $this->target->populateValues($data);
    }
}
