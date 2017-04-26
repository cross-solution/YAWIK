<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Entity\Hydrator;

use Core\Entity\EntityInterface;
use Core\Entity\EntityTrait;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\MappingEntityHydrator;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Tests for \Core\Entity\Hydrator\MappingEntityHydrator
 * 
 * @covers \Core\Entity\Hydrator\MappingEntityHydrator
 * @coversDefaultClass \Core\Entity\Hydrator\MappingEntityHydrator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Hydrator
 */
class MappingEntityHydratorTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    /**
     *
     *
     * @var array|null|MappingEntityHydrator
     */
    private $target = [
        MappingEntityHydrator::class,
        '@testConstruct' => false,
    ];

    private $inheritance = [ EntityHydrator::class ];

    private $properties = [
        ['propertyMap', ['default' => [], 'value' => ['property' => 'fieldname']]]
    ];

    /**
     * @testdox Allows setting property map when constructing instance
     * @covers ::__construct()
     */
    public function testConstruct()
    {
        $map = ['test' => 'works'];
        $target = new MappingEntityHydrator($map);

        $this->assertEquals($map, $target->getPropertyMap());
    }

    /**
     * @covers ::extract()
     */
    public function testExtraction()
    {
        $entity = new Meht_Entity();
        $entity->setOne('one');
        $entity->setTwo('two');

        $this->target->setPropertyMap(['one' => 'three']);

        $actual = $this->target->extract($entity);
        $expect = [
            'three' => 'one',
            'two' => 'two',
        ];

        $this->assertEquals($expect, $actual);
    }

    /**
     * @covers ::hydrate()
     */
    public function testHydration()
    {
        $this->target->setPropertyMap(['one' => 'three']);

        $data = [
            'three' => 'one',
            'two' => 'two',
        ];

        $entity = new Meht_Entity();

        $this->target->hydrate($data, $entity);

        $this->assertEquals('one', $entity->one);
    }

}

class Meht_Entity implements EntityInterface
{
    use EntityTrait;

    public $one;
    public $two;

    public function setOne($one)
    {
        $this->one = $one;

        return $this;
    }

    public function getOne()
    {
        return $this->one;
    }

    public function setTwo($two)
    {
        $this->two = $two;

        return $this;
    }

    public function getTwo()
    {
        return $this->two;
    }
}