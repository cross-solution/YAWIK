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

use PHPUnit\Framework\TestCase;

use Core\Options\Exception\MissingOptionException;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Tests for \Core\Options\Exception\MissingOptionException
 *
 * @covers \Core\Options\Exception\MissingOptionException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Options
 * @group Core.Options.Exception
 */
class MissingDependencyExceptionTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    protected $target = [
        'class' => '\Core\Options\Exception\MissingOptionException',
        'args' => [ 'testOptionKey', 'TestTarget' ]
    ];

    protected $inheritance = [
        '\Core\Exception\ExceptionInterface',
        '\Core\Options\Exception\ExceptionInterface',
        '\RuntimeException',
    ];

    public function propertiesProvider()
    {
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
