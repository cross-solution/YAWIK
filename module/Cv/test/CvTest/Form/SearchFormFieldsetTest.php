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

use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Form\SearchFormFieldset;

/**
 * Tests for \Cv\Form\SearchFormFieldset
 * 
 * @covers \Cv\Form\SearchFormFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Test
 */
class SearchFormFieldsetTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    private $target = [
        SearchFormFieldset::class,
        '@testAllowsSettingLocationEngineTypeViaConstructorOptions' => false,
        '@testInitializesItself' => [
            'args' => [null, ['location_engine_type' => 'locengine']],
            'mock' => [
                'setName' => ['with' => 'params', 'count' => 1],
                'add'
            ],
        ],
    ];

    private $inheritance = ['Zend\Form\Fieldset'];

    public function testAllowsSettingLocationEngineTypeViaConstructorOptions()
    {
        $target = new SearchFormFieldset();

        $this->assertAttributeEmpty('locationEngineType', $target);

        $target = new SearchFormFieldset(null, ['location_engine_type' => 'locationEngine']);

        $this->assertAttributeEquals('locationEngine', 'locationEngineType', $target);
    }

    public function testInitializesItself()
    {
        $this->target
            ->expects($this->exactly(3))
            ->method('add')
            ->withConsecutive(
                [
                    [
                        'name' => 'search',
                        'options' => [
                            'label' =>  'Search for resumes'
                        ]
                    ],
                ],
                [
                    [
                        'name' => 'l',
                        'type' => 'Location',
                        'options' => [
                            'label' =>  'Location',
                            'engine_type' => 'locengine',
                        ],
                    ]
                ],
                [
                    [
                        'name' => 'd',
                        'type' => 'Zend\Form\Element\Select',
                        'options' => [
                            'label' =>  'Distance',
                            'value_options' => [
                                '5' => '5 km',
                                '10' => '10 km',
                                '20' => '20 km',
                                '50' => '50 km',
                                '100' => '100 km'
                            ],

                        ],
                        'attributes' => [
                            'value' => '10',
                            'data-searchbox' => -1,
                            'data-allowclear' => 'false',
                            'data-placeholder' =>  'Distance',
                        ]
                    ]
                ]
            )
        ;

        $this->target->init();


    }
}