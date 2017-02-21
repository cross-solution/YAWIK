<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form;

use Core\Entity\Hydrator\EntityHydrator;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Jobs\Entity\Location;
use Jobs\Form\BaseFieldset;
use Zend\Form\Fieldset;
use Zend\Hydrator\ArraySerializable;

/**
 * Tests for \Jobs\Form\BaseFieldset
 * 
 * @covers \Jobs\Form\BaseFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class BaseFieldsetTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = [
        BaseFieldset::class,
        '@testInitialize' => [
            'mock' => ['setAttribute' => ['with' => ['id', 'job-fieldset']], 'setName' => ['with' => 'jobBase'], 'add'],
        ],
    ];

    private $inheritance = [ Fieldset::class ];

    public function propertiesProvider()
    {
        return [
            ['locationEngineType', ['setter_value' => null, 'expect_property' => 'test', 'value' => 'test']],
            /* todo fix this test */ // ['hydrator', ['value' => new ArraySerializable(), 'default@' => EntityHydrator::class]],
        ];
    }

    public function testInitialize()
    {
        $this->target->expects($this->exactly(2))->method('add')
            ->withConsecutive(
                [
                    [
                        'type' => 'Text',
                        'name' => 'title',
                        'options' => [
                            'label' => /*@translate*/ 'Job title',
                            'description' => /*@translate*/ 'Please enter the job title'
                        ],
                    ]
                ],
                [
                    [
                        'type' => 'LocationSelect',
                        'name' => 'geoLocation',
                        'options' => [
                            'label' => /*@translate*/ 'Location',
                            'description' => /*@translate*/ 'Please enter the location of the job',
                            'location_entity' => Location::class,
                            'summary_value' => function() {},
                        ],
                        'attributes' => [
                            'data-width' => '100%'
                        ]
                    ]
                ]
            );

        $this->target->setLocationEngineType('test');
        $this->target->init();
    }
}