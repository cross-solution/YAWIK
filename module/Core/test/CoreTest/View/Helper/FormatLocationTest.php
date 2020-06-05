<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright 2020 CROSS Solution <https://www.cross-solution.de>
 * @license MIT
 */

declare(strict_types=1);

namespace CoreTest\View\Helper;

use Core\Entity\AbstractLocation;
use Core\Entity\LocationInterface;
use PHPUnit\Framework\TestCase;
use Core\View\Helper\FormatLocation;
use Doctrine\Common\Collections\ArrayCollection;
use Geo\Entity\Geometry\Point;
use Laminas\View\Helper\AbstractHelper;

/**
 * Testcase for \Core\View\Helper\FormatLocation
 *
 * @covers \Core\View\Helper\FormatLocation
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.View
 * @group Core.View.Helper
 * @group Core.View.Helper.FormatLocation
 */
class FormatLocationTest extends TestCase
{

    public function testInheritance()
    {
        static::assertTrue(is_a(FormatLocation::class, AbstractHelper::class, true));
    }

    private function getLocation($prefix='')
    {
        $location = new class extends AbstractLocation implements LocationInterface
        {  };

        $location->setStreetname($prefix . 'TestStreet');
        $location->setStreetnumber($prefix . '1234');
        $location->setCity($prefix . 'TestCity');
        $location->setCoordinates(new Point([1,1]));
        $location->setCountry($prefix . 'TestCountry');
        $location->setPostalCode($prefix . 'ZipCode');
        $location->setRegion($prefix . 'TestRegion');

        return $location;
    }

    private function getLocationCollection()
    {
        $loc1 = $this->getLocation();
        $loc2 = $this->getLocation('Two:');

        return new ArrayCollection([$loc1, $loc2]);
    }

    public function testInvokationProxiesToCorrectMethods()
    {
        $target = new class extends FormatLocation
        {
            public $args = [
                'format' => false,
                'formatCollection' => false,
            ];

            public function format(\Core\Entity\LocationInterface $location, ?string $format = null)
            {
                $this->args['format'] = func_get_args();
            }

            public function formatCollection(\Doctrine\Common\Collections\Collection $locations, ?string $format = null, string $separator = '<br>')
            {
                $this->args['formatCollection'] = func_get_args();
            }
        };

        static::assertSame($target, $target(), 'Invokation w/o args does not return self!');

        $loc = $this->getLocation();
        $target($loc, 'format', 'separator');

        static::assertEquals([$loc, 'format'], $target->args['format'], 'Fail to proxy to "format".');

        $col = $this->getLocationCollection();
        $target($col, 'format', 'separator');

        static::assertEquals([$col, 'format', 'separator'], $target->args['formatCollection'], 'Fail to proxy to "formatCollection".');
    }

    public function testFormatCollection()
    {
        $target = new class extends FormatLocation
        {
            public function format(\Core\Entity\LocationInterface $location, ?string $format = null)
            {
                return 'formattedlocation';
            }
        };

        $col = $this->getLocationCollection();

        static::assertEquals(
            'formattedlocation-formattedlocation',
            $target->formatCollection($col, null, '-')
        );
    }


    public function provideFormatTestData()
    {
        return [
            [null, 'TestStreet 1234, ZipCode TestCity, TestRegion, TestCountry'],
            [
                '%c:--%C%z%Z%r%s%S%n : %lon %lat',
                'TestCity--TestCountry, ZipCode, ZipCode TestCity, TestRegion, TestStreet, TestStreet 1234, 1234 : 1 1'
            ],
        ];
    }
    /**
     * @dataProvider provideFormatTestData
     */
    public function testFormat($format, $expect)
    {
        $target = new FormatLocation();
        $loc = $this->getLocation();

        $actual = $target->format($loc, $format);

        static::assertEquals($actual, $expect);
    }

    public function testFormatWithEmptyValues()
    {
        $target = new FormatLocation();
        $loc = $this->getLocation();
        $loc->setCity('');

        $actual = $target->format($loc, '%s%c%C');

        static::assertEquals($actual, 'TestStreet, TestCountry');
    }
}
