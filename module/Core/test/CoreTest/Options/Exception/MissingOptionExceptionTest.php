<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\OptionsException;

use Core\Options\Exception\MissingOptionException;

/**
 * Tests for \Core\Options\Exception\MissingOptionException
 * 
 * @covers \Core\Options\Exception\MissingOptionException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Exception
 * @since 0.20
 */
class MissingDependencyExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox Extends \RuntimeException and implements \Core\Option\Exception\ExceptionInterface
     * @coversNothing
     */
    public function testExtendsRuntimeException()
    {
        $target = new MissingOptionException('test', 'test');

        $this->assertInstanceOf('\RuntimeException', $target);
        $this->assertInstanceOf('\Core\Options\Exception\ExceptionInterface', $target);
    }


    public function provideConstructionTestData()
    {
        return array(
            array('testKeyOne', 'testTarget', null),
            array('testKeyTwo', new \stdClass(), null),
            array('testKeyThree', 'testTargetTwo', new \Exception('testPreviousException'))
        );
    }
    /**
     * @testdox Allows creation by passing the missing option key and target object
     * @dataProvider provideConstructionTestData
     *
     * @param string $optionKey
     * @param string|object $object
     * @param \Exception|null $previous
     */
    public function testSetsCorrectExceptionMessage($optionKey, $object, $previous)
    {
        $target = new MissingOptionException($optionKey, $object, $previous);

        $expectedMessage = sprintf('Missing value for option "%s" in "%s"',
                                   $optionKey, is_object($object) ? get_class($object) : $object);

        $this->assertEquals($expectedMessage, $target->getMessage());
    }

    /**
     * @testdox Provided dependency FQCN and target information are retrievable
     */
    public function testSetsCorrectProperties()
    {
        $expectedDep = 'testKey';
        $expectedObj = 'testObj';

        $target = new MissingOptionException('testKey', 'testObj');

        $this->assertAttributeEquals($expectedDep, 'optionKey', $target);
        $this->assertAttributeEquals($expectedObj, 'target', $target);
    }


    /**
     * @testdox Provided dependency FQCN and target information are retrievable
     * @dataProvider provideConstructionTestData
     */
    public function testGetter($optionKey, $obj, $prev)
    {
        $target = new MissingOptionException($optionKey, $obj);
        $expObjFqcn = is_object($obj) ? get_class($obj) : $obj;

        $this->assertEquals($optionKey, $target->getOptionKey(), 'dependency assertion failed.');
        $this->assertEquals($obj, $target->getTarget(), 'target assertion failed.');
        $this->assertEquals($expObjFqcn, $target->getTargetFQCN(), 'target fqcn assertion failed.');
    }
}