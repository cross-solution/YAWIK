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

use Core\Entity\AbstractLocation;
use Core\Entity\LocationInterface;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Geo\Form\GeoSelectHydratorStrategy;
use Geo\Service\AbstractClient;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Tests for \Geo\Form\GeoSelectHydratorStrategy
 * 
 * @covers \Geo\Form\GeoSelectHydratorStrategy
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Geo
 * @group Geo.Form
 */
class GeoSelectHydratorStrategyTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = [
        GeoSelectHydratorStrategy::class,
        'getTargetArgs',
        '@testConstruction' => false,
        '@testInheritance' => [ 'as_reflection' => true ],
    ];

    private $inheritance = [ StrategyInterface::class ];

    private function getTargetArgs()
    {
        $client = $this->getMockBuilder(AbstractClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['queryOne'])
            ->getMockForAbstractClass()
        ;
        $this->clientMock = $client;

        return [ $client ];
    }

    public function propertiesProvider()
    {
        $location = new Gshst_Location();
        $assertInstance = function() { $this->assertInstanceOf(Gshst_Location::class, $this->target->getLocationEntity()); };
        return [
            ['locationEntityPrototype', [
                'value' => Gshst_Location::class,
                'ignore_getter' => true,
                'post' => $assertInstance,
            ]],
            ['locationEntity', [
                'value' => $location,
                'setter_method' => 'set*Prototype',
                'ignore_getter' => true,
                'post' => $assertInstance,
            ]],
        ];
    }

    public function testConstruction()
    {
        $client = $this->getTargetArgs()[0];
        $target = new GeoSelectHydratorStrategy($client);

        $this->assertAttributeSame($client, 'geoClient', $target);
    }

    public function provideExtractionTestData()
    {
        $location = new Gshst_Location();
        $location->setCity('TestCity');

        return [
            [$location, $location->toString()],
            ['{city:Test}', '{city:Test}'],
            ['City', 'queryCity', true],
            [[$location], [$location->toString()]],
            [null, null],
            ['', null],
        ];
    }

    /**
     * @dataProvider provideExtractionTestData
     *
     * @param      $value
     * @param      $expect
     * @param bool $expectQueryClient
     */
    public function testExtraction($value, $expect, $expectQueryClient = false)
    {
        if ($expectQueryClient) {
            $this->clientMock->expects($this->once())->method('queryOne')->with($value)->willReturn(['id' => $expect]);
        }

        $this->assertEquals($expect, $this->target->extract($value));
    }

    public function provideHydrationTestData()
    {
        $location = new Gshst_Location();
        $location->setCity('TestCity');
        $locStr = $location->toString();
        $coll = new ArrayCollection([$location]);
        return [
            [null, null],
            ['', null],
            [$locStr, $location],
            [[$locStr], $coll],
        ];
    }

    /**
     * @dataProvider provideHydrationTestData
     *
     * @param $value
     * @param $expect
     */
    public function testHydration($value, $expect)
    {
        $this->target->setLocationEntityPrototype(new Gshst_Location());

        $this->assertEquals($expect, $this->target->hydrate($value));
    }
}

class Gshst_Location extends AbstractLocation implements LocationInterface
{ }