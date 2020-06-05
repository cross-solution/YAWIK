<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace CoreTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\Timeline;
use CoreTestUtils\TestCase\SetupTargetTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Class TimelineTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package CoreTest\Entity
 * @since 0.30.1
 * @covers \Core\Entity\Timeline
 */
class TimelineTest extends TestCase
{
    use TestSetterGetterTrait, SetupTargetTrait;

    protected $target = [
        'class' => Timeline::class
    ];

    public function propertiesProvider()
    {
        return [
            ['date',[
                'value' => new \DateTime(),
                'default' => new \DateTime(),
                'default_assert' => function ($v, $return) {
                    $date = new \DateTime();
                    $this->assertEquals(
                        $date->getTimestamp(),
                        $return->getTimestamp()
                    );
                },
            ]]
        ];
    }
}
