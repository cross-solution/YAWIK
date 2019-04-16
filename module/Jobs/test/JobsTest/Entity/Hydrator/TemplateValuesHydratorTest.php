<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Entity\Hydrator;

use PHPUnit\Framework\TestCase;

use Jobs\Entity\Hydrator\TemplateValuesHydrator;
use Jobs\Entity\TemplateValues;

/**
 * Tests for \Jobs\Entity\Hydrator\TemplateValuesHydrator
 *
 * @covers \Jobs\Entity\Hydrator\TemplateValuesHydrator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Entity
 * @group Jobs.Entity.Hydrator
 */
class TemplateValuesHydratorTest extends TestCase
{

    /**
     * @testdox Extends \Core\Entity\Hydrator\EntityHydrator
     */
    public function testExtendsEntityHydrator()
    {
        $target = new TemplateValuesHydrator();

        $this->assertInstanceOf('\Core\Entity\Hydrator\EntityHydrator', $target);
    }

    /**
     * @testdox FreeValues keys can be set through the constructor.
     */
    public function testFreeValuesKeysCanBeSetThroughConstructor()
    {
        $keys = [ 'key1', 'key2' ];
        $target = new TemplateValuesHydrator($keys);

        $this->assertAttributeEquals($keys, 'freeValuesKeys', $target);
    }

    /**
     * @testdox Allows setting and getting FreeValues keys.
     */
    public function testSetAndGetFreeValuesKeys()
    {
        $target = new TemplateValuesHydrator();
        $keys = [ 'key1', 'key2' ];

        $this->assertSame($target, $target->setFreeValuesKeys($keys), 'Fluent interface broken.');
        $this->assertEquals($keys, $target->getFreeValuesKeys());
    }

    /**
     * @testdox Extracts a TemplateValues object with the set FreeValues keys.
     */
    public function testExtract()
    {
        $values = new TemplateValues();
        $values->freeKey1 = 'value1';
        $values->ignoredKey = 'irrelephant';

        $target = new TemplateValuesHydrator([ 'freeKey1' ]);

        $data = $target->extract($values);

        $this->assertArrayHasKey('freeKey1', $data);
        $this->assertEquals('value1', $data['freeKey1']);
        $this->assertArrayNotHasKey('ignoredKey', $data);
    }

    /**
     * @testdox Hydrates data to a TemplateValues object including the configured FreeValues.
     */
    public function testHydrate()
    {
        $values = new TemplateValues();
        $target = new TemplateValuesHydrator([ 'testKeyOne' ]);
        $data = [
            'testKeyOne' => 'testValue',
            'ignoredKey' => 'irrelevant'
        ];

        $this->assertSame($values, $target->hydrate($data, $values), 'hydrate() does not return object.');
        $this->assertEquals('testValue', $values->get('testKeyOne'));
        $this->assertNull($values->get('ignoredKey'));
    }
}
