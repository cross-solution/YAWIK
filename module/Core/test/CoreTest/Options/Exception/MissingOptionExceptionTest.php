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
use CoreTestUtils\TestCase\AssertInheritanceTrait;
use CoreTestUtils\TestCase\SetterGetterTrait;

/**
 * Tests for \Core\Options\Exception\MissingOptionException
 * 
 * @covers \Core\Options\Exception\MissingOptionException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Options
 * @group Core.Options.Exception
 */
class MissingDependencyExceptionTest extends \PHPUnit_Framework_TestCase
{
    use AssertInheritanceTrait, SetterGetterTrait;

    protected $target = [ '\Core\Options\Exception\MissingOptionException' , [ 'testOptionKey', 'TestTarget' ] ];

    protected $inheritance = [
        '\Core\Exception\ExceptionInterface',
        '\Core\Options\Exception\ExceptionInterface',
        '\RuntimeException',
    ];

    public function propertiesProvider() {
        $ex = new \Exception();
        $target = new MissingOptionException('testOptionKey', 'TestTarget', $ex);
        $target2 = new MissingOptionException('-', new \stdClass());

        return [
            [ 'OptionKey', [
                'value' => 'testOptionKey',
                'ignore_setter' => true,
            ]],
            [ 'target', [
                'value' => 'TestTarget',
                'ignore_setter' => true,
            ]],
            [ 'targetFQCN', [
                'value' => 'stdClass',
                'target' => $target2,
                'ignore_setter' => true,
            ]],
            [ 'targetFQCN', [
                'value' => 'TestTarget',
                'ignore_setter' => true,
            ]],
            [ 'previous', [
                'value' => $ex,
                'target' => $target,
                'ignore_setter' => true,
            ]],
            [ 'message', [
                'value' => 'Missing value for option "testOptionKey" in "TestTarget"',
                'ignore_setter' => true,
            ]]
        ];
    }
}