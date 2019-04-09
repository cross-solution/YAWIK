<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Form\Hydrator;

use PHPUnit\Framework\TestCase;

use Core\Entity\MetaDataProviderInterface;
use Core\Form\Hydrator\MetaDataHydrator;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\Hydrator\HydratorInterface;

/**
 * Tests for \Core\Form\Hydrator\MetaDataHydrator
 *
 * @covers \Core\Form\Hydrator\MetaDataHydrator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 * @group Core.Form.Hydrator
 */
class MetaDataHydratorTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var MetaDataHydrator|\PHPUnit_Framework_MockObject_MockObject|string
     */
    private $target = MetaDataHydrator::class;

    private $inheritance = [ HydratorInterface::class ];

    public function testExtractionOfNonMetaDataProvider()
    {
        $object = new \stdClass();

        $this->assertEquals([], $this->target->extract($object));
    }

    public function testExtractionOfMetaDataProvider()
    {
        $expect = ['extract' => 'works'];
        $object = $this->getMockBuilder(MetaDataProviderInterface::class)
                       ->setMethods(['getMetaData'])
                       ->getMockForAbstractClass();

        $object->expects($this->once())->method('getMetaData')->willReturn($expect);

        $actual = $this->target->extract($object);

        $this->assertEquals($expect, $actual);
    }

    public function testHydrationOfNonMetaDataProvider()
    {
        $object = new \stdClass;

        $this->assertSame($object, $this->target->hydrate([], $object));
    }

    public function testHydrationOfMetaDataProvider()
    {
        $object = $this->getMockBuilder(MetaDataProviderInterface::class)
                       ->setMethods(['setMetaData'])
                       ->getMockForAbstractClass();

        $object->expects($this->once())->method('setMetaData')->with('key', 'value');

        $this->assertSame($object, $this->target->hydrate(['key' => 'value'], $object));
    }
}
