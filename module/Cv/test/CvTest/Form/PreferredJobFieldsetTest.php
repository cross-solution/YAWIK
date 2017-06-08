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
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Entity\Location;
use Cv\Entity\PreferredJob;
use Cv\Form\PreferredJobFieldset;
use Zend\Form\Fieldset;

/**
 * Tests for \Cv\Form\PreferredJobFieldset
 * 
 * @covers \Cv\Form\PreferredJobFieldset
 * @coversDefaultClass \Cv\Form\PreferredJobFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 */
class PreferredJobFieldsetTest extends \PHPUnit_Framework_TestCase
{

    use TestInheritanceTrait, TestDefaultAttributesTrait;

    /**
     *
     *
     * @var array|PreferredJobFieldset|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        PreferredJobFieldset::class,
        '@testInitializesItself' => [
            'mock' => [
                'add',
                'setName' => ['with' => 'preferredJob', 'count' => 1, 'return' => '__self__'],
                'setHydrator' => ['@with' => ['isInstanceOf', EntityHydrator::class ], 'count' => 1, 'return' => '__self__'],
                'setObject' => ['@with' => ['isInstanceOf', PreferredJob::class ], 'count' => 1, 'return' => '__self__'],
            ],
            'args' => false,
        ],
    ];

    private $inheritance = [ Fieldset::class ];

    private $attributes = [
        'typeOfApplicationOptions' => [
            '' => '',
            "temporary" =>  "Temporary",
            "permanent" =>  "Permanent",
            "contract"=>  "Contracting",
            "freelance" =>  "Freelance",
            "internship" =>  "Internship"
        ],
        'willingnessToTravelOptions' => [
            '' => '',
            "yes"=> "Yes",
            "conditioned" =>  "conditioned",
            "no"=> "No"
        ],
        'defaultEmptySummaryNotice' =>  'Click here to enter your employment expectation',
        'defaultPartial' => 'cv/form/preferred-job-fieldset',
    ];

    /**
     * @covers ::init()
     */
    public function testInitializesItself()
    {
        $add = [
                 [
                     'name' => 'typeOfApplication',
                     'type' => 'select',
                     'options' => [
                         'value_options' => $this->attributes['typeOfApplicationOptions'],
                         'label' =>  'desired type of work',
                         'description' =>  'Do you want to work permanently or temporary?',
                     ],
                     'attributes' => [
                         'title' =>  'please describe your position',
                         'description' => 'what kind of ',
                         'data-placeholder' =>  'please select',
                         'data-allowclear' => 'false',
                         'data-searchbox' => -1,
                         'multiple' => true,
                         'data-width' => '100%',
                     ],
                 ],

             [
                 'name' => 'desiredJob',
                 'type' => 'Text',
                 'options' => [
                     'label' =>  'desired job position',
                     'description' =>  'Enter the title of your desired job. Eg. "Software Developer" or "Customer Service Representative"',
                 ],
                 'attributes' => [
                     'title' =>  'please describe your position',
                 ],
             ],
             [
                 'name' => 'desiredLocations',
                 'type' => 'LocationSelect',
                 'options' => [
                     'label' =>  'desired job location',
                     'description' =>  'Where do you want to work?',
                     'location_entity' => new Location(),
                 ],
                 'attributes' => [
                     'title' =>  'please describe your position',
                     'multiple' => true,
                     'data-width' => '100%',
                 ],
             ],
             [
                 'name' => 'willingnessToTravel',
                 'type' => 'Select',
                 'options' => [
                     'value_options' => $this->attributes['willingnessToTravelOptions'],
                     'label' =>  'Willingness to travel',
                     'description' =>  'Enter your willingness to travel.',
                 ],
                 'attributes' => [
                     'data-placeholder' =>  'please select',
                     'data-allowclear' => 'false',
                     'data-searchbox' => -1,
                     'data-width' => '100%'
                 ],
             ],
             [
                 'name' => 'expectedSalary',
                 'type' => 'Text',
                 'options' => [
                     'label' =>  'expected Salary',
                     'description' =>  'What is your expected Salary?',
                 ],
                 'attributes' => [
                     'title' =>  'please describe your position',
                 ],
             ],
        ];

        $callCount = count($add);
        $addArgValidator = function($arg) use ($add, $callCount) {
            static $count = 0;

            /* PPHUnit calls this callback again after all invokations are made
             * I don't know why, but therefor the need to check if $count is greater that 7
             */
            return $callCount - 1  < $count || $arg == $add[$count++];
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