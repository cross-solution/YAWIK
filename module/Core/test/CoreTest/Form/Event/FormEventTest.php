<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Form\Event;

use PHPUnit\Framework\TestCase;

use Core\Form\Container;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Zend\Form\Form;

/**
 * Tests for \Core\Form\Event\FormEvent
 *
 * @covers \Core\Form\Event\FormEvent
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 * @group Core.Form.Event
 */
class FormEventTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    /**
     *
     * @var string|\Core\Form\Event\FormEvent
     */
    protected $target = '\Core\Form\Event\FormEvent';

    protected $inheritance = [ '\Zend\EventManager\Event' ];

    public function propertiesProvider()
    {
        $form = new Form();
        $container = new Container();
        return [
            [ 'Form', '@\Zend\Form\Form' ],
            [ 'Form', '@\Core\Form\Container' ],
            [ 'Form', [ 'value' => 'invalid', 'setter_exception' => ['\InvalidArgumentException', 'Form must either' ] ] ],
            [ 'Target', 'TestTarget' ],
            [ 'Target', [
                'value' => $form,
                'expect_property' => [ 'form', $form ]
            ]],
            [ 'Target', [
                'value' => $container,
                'expect_property' => [ 'form', $container ],
            ]],
            [ 'Target', [
                'value' => 'NonFormOrContainer',
                'expect_property' => [ 'form', null ],
            ]],
            [ 'Params', [ 'value' => [ 'param1' => 'value1' ]]],
            [ 'Params', [
                'value' => [ 'form' => $form ],
                'expect_property' => [ 'form', $form ]
            ]],
            [ 'Params', [
                'value' => [ 'form' => $container ],
                'expect_property' => [ 'form', $container ]
            ]],
            [ 'Params', [
                'value' => [ 'form' => 'notAnFormInstance' ],
                'expect_property' => [ 'form', null ]
            ]]
        ];
    }
}
