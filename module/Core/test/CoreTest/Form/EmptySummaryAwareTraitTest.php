<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace CoreTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Form\EmptySummaryAwareTrait;
use CoreTestUtils\TestCase\SetupTargetTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * @covers \Core\Form\EmptySummaryAwareTrait
 * @author fedys
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.26
 */
class EmptySummaryAwareTraitTest extends TestCase
{
    use SetupTargetTrait, TestSetterGetterTrait;

    private $target = [
        EmptySummaryAwareTraitMock::class,
        '@testSetterAndGetter|#3' => EmptySummaryAwareTraitWithDefaultNoticeMock::class,
    ];

    public function propertiesProvider()
    {
        return  [
            [ 'summaryEmpty', [
                'ignore_setter' => true,
                'pre' => function () {
                    $this->target->add(['name' => 'test', 'type' => 'text', 'attributes' => ['value'=>'test']]);
                },
                'value' => false,
                'getter_method' => 'is*'
            ]],
            [ 'summaryEmpty', [
                'ignore_setter' => true,
                'pre' => function () {
                    $this->target->add(['name' => 'test', 'type' => 'text']);
                },
                'value' => true,
                'getter_method' => 'is*',
            ]],
            [ 'emptySummaryNotice', [
                'value' => 'notice',
                'default' => null
            ]],
            [ 'emptySummaryNotice', [
                'value' => 'notice',
                'default' => 'defaultNotice',
            ]],

        ];
    }
}

class EmptySummaryAwareTraitMock extends \Zend\Form\Fieldset
{
    use EmptySummaryAwareTrait;
}
class EmptySummaryAwareTraitWithDefaultNoticeMock extends \Zend\Form\Fieldset
{
    private $defaultEmptySummaryNotice = 'defaultNotice';
    use EmptySummaryAwareTrait;
}
