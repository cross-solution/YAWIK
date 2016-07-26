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
use Cv\Entity\Education;
use Cv\Form\EducationFieldset;
use Zend\Form\Fieldset;

/**
 * Tests for \Cv\Form\EducationFieldset
 * 
 * @covers \Cv\Form\EducationFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 */
class EducationFieldsetTest extends \PHPUnit_Framework_TestCase
{

    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|EducationFieldset|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        EducationFieldset::class,
        '@testInitializesItself' => [
            'mock' => [
                'add',
                'setName' => ['with' => 'education', 'count' => 1, 'return' => '__self__'],
                'setHydrator' => ['@with' => ['isInstanceOf', EntityHydrator::class ], 'count' => 1, 'return' => '__self__'],
                'setObject' => ['@with' => ['isInstanceOf', Education::class ], 'count' => 1, 'return' => '__self__'],
                'setLabel' => ['with' => 'Education', 'count' => 1]
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
                    'data-width' => '50%',
                    'class' => 'selectpicker'
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
                 'name' => 'competencyName',
                 'options' => [
                     'label' => 'Degree'],
                 'attributes' => [
                     'title' =>  'please enter the name of your qualification'
                 ],
             ],
             [
                 'name' => 'organizationName',
                 'options' => [
                     'label' => 'Organization Name'],
                 'attributes' => [
                     'title' =>  'please enter the name of the university or school'
                 ],
             ],
            [
                 'name' => 'country',
                 'options' => [
                     'label' => 'Country'],
                 'attributes' => [
                     'title' => /*@translate */ 'please select the country'
                 ],
             ],
             [
                 'name' => 'city',
                 'options' => [
                     'label' => 'City'],
                 'attributes' => [
                     'title' => 'please enter the name of the city'
                 ],
             ],
             [
                 'name' => 'description',
                 'type' => 'Zend\Form\Element\Textarea',
                 'options' => [
                     'label' => 'Description',
                 ],
                 'attributes' => [
                     'title' => 'please enter a description',
                 ],
             ],
        ];

        $addArgValidator = function($arg) use ($add) {
            static $count = 0;

            /* PPHUnit calls this callback again after all invokations are made
             * I don't know why, but therefor the need to check if $count is greater that 7
             */
            return 7 < $count || $arg === $add[$count++];
        };

        $this->target
            ->expects($this->exactly(count($add)))
            ->method('add')
            ->with($this->callback($addArgValidator))
            ->will($this->returnSelf())
        ;

        $this->target->init();
    }
}