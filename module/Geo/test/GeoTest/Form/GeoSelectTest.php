<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace GeoTest\Form;

use Core\Form\HeadscriptProviderInterface;
use Core\Form\Hydrator\HydratorStrategyProviderInterface;
use Core\Form\Hydrator\HydratorStrategyProviderTrait;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use Geo\Form\GeoSelect;
use Geo\Form\GeoSelectHydratorStrategy;
use Geo\Service\AbstractClient;
use Zend\Form\Element\Select;
use Zend\Json\Json;

/**
 * Tests for \Geo\Form\GeoSelect
 * 
 * @covers \Geo\Form\GeoSelect
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Geo
 * @group Geo.Form
 */
class GeoSelectTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait, TestDefaultAttributesTrait, TestSetterGetterTrait;

    private $target = [
        GeoSelect::class,
        '@testInit' => [
            'mock' => [
                'setAttributes' => [
                    'with' => [[
                        'data-placeholder' => 'Location',
                        'data-autoinit' => false,
                        'class' => 'geoselect',
                         'data-clear-on-reset' => true
                    ]],
                    'count' => 1
                ],
            ],
        ],
    ];

    private $inheritance = [ Select::class, HeadscriptProviderInterface::class, HydratorStrategyProviderInterface::class ];

    private $traits = [ HydratorStrategyProviderTrait::class ];

    private $attributes = [
        'disableInArrayValidator' => true,
        'headscripts' => [ 'Geo/js/geoselect.js' ],
    ];

    public function propertiesProvider()
    {
        return [
            ['value', [
                'value' => 'value',
                'post'  => function() { $this->assertEquals('value', $this->target->getAttribute('data-val')); },
            ]],
            ['value', [
                'value' => ['one', 'two'],
                'post' => function() { $this->assertEquals(Json::encode(['one', 'two']), $this->target->getAttribute('data-val')); },
            ]],
            ['headscripts', [
                'default' => ['Geo/js/geoselect.js'],
                'value'   => ['Some/other/script.js'],
            ]],
        ];
    }



    public function testSetLocationEntityViaOptions()
    {
        $strategy = $this->getMockBuilder(GeoSelectHydratorStrategy::class)->disableOriginalConstructor()
            ->setMethods(['setLocationEntityPrototype'])->getMock();
        $strategy->expects($this->once())->method('setLocationEntityPrototype')->with('locationEntity');

        $this->target->setHydratorStrategy($strategy);
        $options = [
            'location_entity' => 'locationEntity'
        ];

        $this->target->setOptions($options);
    }

    public function testInit()
    {
        $this->target->init();
    }
}