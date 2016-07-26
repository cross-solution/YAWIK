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

use Core\Entity\Hydrator\EntityHydrator;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Entity\Employment;
use Cv\Form\EmploymentFieldset;
use Zend\Form\Fieldset;

/**
 * Tests for \Cv\Form\EmploymentFieldset
 * 
 * @covers \Cv\Form\EmploymentFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 */
class EmploymentFieldsetTest extends \PHPUnit_Framework_TestCase
{

    use TestInheritanceTrait;

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
                'setLabel' => ['with' => 'Employment', 'count' => 1]
            ],
            'args' => false,
        ],
    ];

    private $inheritance = [ Fieldset::class ];

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
                     'label' => 'Company Name'
                 ],
                 'attributes' => [
                     'title' =>  'please enter the name of the company'
                 ],
             ],
             [
                 'name' => 'description',
                 'type' => 'Zend\Form\Element\Textarea',
                 'options' => [
                     'label' => 'Description'],
                 'attributes' => [
                     'title' =>  'please describe your position'
                 ],
             ],
        ];

        $callCount = count($add);
        $addArgValidator = function($arg) use ($add, $callCount) {
            static $count = 0;

            /* PPHUnit calls this callback again after all invokations are made
             * I don't know why, but therefor the need to check if $count is greater that 7
             */
            return $callCount - 1  < $count || $arg === $add[$count++];
        };

        $this->target
            ->expects($this->exactly($callCount))
            ->method('add')
            ->with($this->callback($addArgValidator))
            ->will($this->returnSelf())
        ;

        $this->target->init();
    }
}