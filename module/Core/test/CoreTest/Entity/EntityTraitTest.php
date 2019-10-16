<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CoreTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\EntityInterface;
use Core\Entity\EntityTrait;

/**
 * Test the EntityTrait
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Core
 * @group  Core.Entity
 * @covers \Core\Entity\EntityTrait
 */
class EntityTraitTest extends TestCase
{
    protected static $fileResource;
    protected $target;


    public static function tearDownAfterClass(): void
    {
        fclose(self::$fileResource);
    }


    protected function setUp(): void
    {
        $this->target = new TraitEntity();
    }

    public function provideNotEmptyTestData()
    {
        self::$fileResource = tmpfile();
        $emptyCountable = new \ArrayObject();
        $countable = new \ArrayObject(['not', 'empty']);

        return [
            [ null, false ],
            [ '', false ],
            [ 'something', true],
            [ 0, false ],
            [ 1, true ],
            [ false, false ],
            [ true, true ],
            [ [], false ],
            [ ['not', 'empty'], true ],
            [ new \stdClass(), true ],
            [ $emptyCountable, false ],
            [ $countable, true ],
            [ self::$fileResource, true ],

        ];
    }

    /**
     * @dataProvider provideNotEmptyTestData
     *
     * @param $value
     * @param $expected
     */
    public function testNotEmpty($value, $expected)
    {
        $assert = $expected ? 'assertTrue' : 'assertFalse';

        $this->target->setAttribute($value);
        $this->$assert($this->target->notEmpty('attribute'));
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage is not a valid property
     */
    public function testInvalidAttributeThrowsException()
    {
        $this->target->notEmpty('inexistent');
    }

    public function testExtraArgsArePassedToGetterMethod()
    {
        $args = [
            'arg1' => 'testArg1',
            'arg2' => 'testArg2',
        ];

        $actual = $this->target->notEmpty('attributeWithExtraArgs', $args);

        $this->assertAttributeEquals($args, 'extraArgs', $this->target);
    }

    public function hasPropertyTestProvider()
    {
        $facileTarget = new HasPropertyTestTraitEntity();
        $getterTarget = new HasPropertyAndGetterTestTraitEntity();
        $setterTarget = new HasPropertyAndSetterTestTraitEntity();
        $strictTarget = new HasPropertyAndGetterAndSetterTestTraitEntity();
        return [
            [ $facileTarget, [true, false ,false, false]],
            [ $getterTarget, [true, true, false, false]],
            [ $setterTarget, [true, false, true, false]],
            [ $strictTarget, [true, true, true, true]],
        ];
    }

    /**
     * @dataProvider hasPropertyTestProvider
     *
     * @param $target
     * @param $expects
     */
    public function testHasPropertyReturnsExpectedResults($target, $expects)
    {
        $modes = [EntityInterface::PROPERTY_FACILE,
                  EntityInterface::PROPERTY_GETTER,
                  EntityInterface::PROPERTY_SETTER,
                  EntityInterface::PROPERTY_STRICT];

        foreach ($modes  as $mode) {
            $actual = $target->hasProperty('testProperty', $mode);
            $expect = array_shift($expects);
            $assert = 'assert' . ($expect ? 'true' : 'false');

            $this->$assert($actual);
        }
    }
}

class TraitEntity implements EntityInterface
{
    use EntityTrait;

    protected $attribute;
    protected $extraArgs;

    /**
     * @param mixed $attribute
     *
     * @return self
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    public function getAttributeWithExtraArgs($arg1, $arg2)
    {
        $this->extraArgs = compact('arg1', 'arg2');

        return 'irrelevant';
    }
}

class HasPropertyTestTraitEntity implements EntityInterface
{
    use EntityTrait;

    protected $testProperty;
}

class HasPropertyAndGetterTestTraitEntity extends HasPropertyTestTraitEntity
{
    public function getTestProperty()
    {
    }
}

class HasPropertyAndSetterTestTraitEntity extends HasPropertyTestTraitEntity
{
    public function setTestProperty()
    {
    }
}

class HasPropertyAndGetterAndSetterTestTraitEntity extends HasPropertyAndGetterTestTraitEntity
{
    public function setTestProperty()
    {
    }
}
