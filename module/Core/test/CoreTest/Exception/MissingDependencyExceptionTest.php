<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Exception;

use PHPUnit\Framework\TestCase;

use Core\Exception\MissingDependencyException;

/**
 * Tests for \Core\Exception\MissingDependencyException
 *
 * @covers \Core\Exception\MissingDependencyException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Exception
 */
class MissingDependencyExceptionTest extends TestCase
{
    /**
     * @testdox Extends \RuntimeException
     * @coversNothing
     */
    public function testExtendsRuntimeException()
    {
        $target = new MissingDependencyException('test', 'test');

        $this->assertInstanceOf('\RuntimeException', $target);
    }


    public function provideConstructionTestData()
    {
        return array(
            array('testDependencyOne', 'testTarget', null),
            array('testDependencyTwo', new \stdClass(), null),
            array('testDependendyThree', 'testTargetTwo', new \Exception('testPreviousException'))
        );
    }
    /**
     * @testdox Allows creation by passing the missing dependency name and target object
     * @dataProvider provideConstructionTestData
     *
     * @param string $dependency
     * @param string|object $object
     * @param \Exception|null $previous
     */
    public function testSetsCorrectExceptionMessage($dependency, $object, $previous)
    {
        $target = new MissingDependencyException($dependency, $object, $previous);

        $expectedMessage = sprintf(
            'Missing dependency "%s" in "%s"',
            $dependency,
            is_object($object) ? get_class($object) : $object
        );

        $this->assertEquals($expectedMessage, $target->getMessage());
    }

    /**
     * @testdox Provided dependency FQCN and target information are retrievable
     */
    public function testSetsCorrectProperties()
    {
        $expectedDep = 'testDep';
        $expectedObj = 'testObj';

        $target = new MissingDependencyException('testDep', 'testObj');

        $this->assertAttributeEquals($expectedDep, 'dependency', $target);
        $this->assertAttributeEquals($expectedObj, 'target', $target);
    }


    /**
     * @testdox Provided dependency FQCN and target information are retrievable
     * @dataProvider provideConstructionTestData
     */
    public function testGetter($dep, $obj, $prev)
    {
        $target = new MissingDependencyException($dep, $obj);
        $expObjFqcn = is_object($obj) ? get_class($obj) : $obj;

        $this->assertEquals($dep, $target->getDependency(), 'dependency assertion failed.');
        $this->assertEquals($obj, $target->getTarget(), 'target assertion failed.');
        $this->assertEquals($expObjFqcn, $target->getTargetFQCN(), 'target fqcn assertion failed.');
    }
}
