<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Form\Container;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Tests for \Core\Form\WizardContainer
 *
 * @covers \Core\Form\WizardContainer
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 */
class WizardContainerTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    protected $target = '\Core\Form\WizardContainer';

    protected $inheritance = [
        '\Core\Form\Container',
        '\Core\Form\HeadscriptProviderInterface',
        '\IteratorAggregate',
    ];

    public function propertiesProvider()
    {
        $object = new \stdClass();
        $container = new Container();
        $labeldContainer = new Container();
        $labeldContainer->setLabel('testLabel');
        $topContainer = new Container();
        $topContainer->setForm('child', [ '__instance__' => $object ])
                     ->setLabel('top');

        return [
            /*[ 'Headscripts', [
                'default' => [ '/assets/twitter-bootstrap-wizard/jquery.bootstrap.wizard.js' ],
                'value'   => [ 'test/scripts' ],
            ]],*/
            [ 'Form', [
                'value' => 'test',
                'setter_args' => [ $object ],
                'setter_exception' => [ '\InvalidArgumentException', 'Tab container must be of the type' ],
            ]],
            [ 'Form', [
                'value' => 'test',
                'setter_args' => [ $container ],
                'setter_exception' => [ '\InvalidArgumentException', 'Container instances must have a label' ],
            ]],
            [ 'Form', [
                'value' => 'test',
                'setter_args' => [ $labeldContainer ],
                'getter_args' => [ 'test', true ],
                'expect' => $labeldContainer,
            ]],
            [ 'Form', [
                'value' => 'test',
                'setter_args' => [ 'Test/Container' ],
                'getter_args' => [ 'test', /*asInstance*/ false ],
                'expect' => [
                    'type' => 'Test/Container',
                    'name' => 'test',
                    'entity' => '*',
                ],
            ]],
            [ 'Form', [
                'value' => 'test',
                'setter_args' => [ [ 'type' => 'Test/Container' ] ],
                'getter_args' => [ 'test', false ],
                'expect' => [
                    'type' => 'Test/Container',
                    'name' => 'test',
                    'entity' => '*',
                ],
            ]],
            [ 'Form', [
                'value' => 'test',
                'setter_args' => [ [ ] ],
                'getter_args' => [ 'test', false ],
                'expect' => [
                    'type' => 'Core/Container',
                    'name' => 'test',
                    'entity' => '*',
                ],
            ]],
            [ 'Form', [
                'value' => 'test',
                'setter_args' => [ [ 'forms' => [] ] ],
                'getter_args' => [ 'test', false ],
                'expect' => [
                    'type' => 'Core/Container',
                    'name' => 'test',
                    'options' => [ 'forms' => [] ],
                    'entity' => '*',
                ]
            ]],
            [ 'Form', [
                'value' => 'test',
                'setter_args' => [ [ '__instance__' => $object ] ],
                'getter_args' => [ 'test' ],
                'getter_exception' => '\UnexpectedValueException'
            ]],
            [ 'Form', [
                'value' => 'test',
                'setter_args' => [ [ '__instance__' => $container ] ],
                'getter_args' => [ 'test' ],
                'getter_exception' => '\UnexpectedValueException'
            ]],
            [ 'Form', [
                'value' => 'test',
                'setter_args' => [ [ '__instance__' => $labeldContainer ] ],
                'getter_args' => [ 'test' ],
                'expect' => $labeldContainer
            ]],
            [ 'Form', [
                'value' => 'test',
                'setter_args' => [ [ '__instance__' => $topContainer ] ],
                'getter_args' => [ 'test.child' ],
                'expect' => $object
            ]],

        ];
    }
}
