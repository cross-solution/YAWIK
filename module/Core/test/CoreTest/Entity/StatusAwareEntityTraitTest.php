<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\AbstractStatusEntity;
use Core\Entity\StatusAwareEntityInterface;
use Core\Entity\StatusAwareEntityTrait;
use Core\Entity\StatusInterface;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Tests for Core\Entity\StatusAwareEntityTrait
 *
 * @covers \Core\Entity\StatusAwareEntityTrait
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class StatusAwareEntityTraitTest extends TestCase
{
    use TestSetterGetterTrait;

    private $target;

    public function propertiesProvider()
    {
        return [

            [ 'status', [
                'value' => new Status(Status::STATE_ONE),
                'target' => new StatusAwareEntity(),
            ]],

            [ 'status', [
                'target' => new ProvideEntityStatusAwareEntity(),
                'default' => true,
                'default_assert' => function ($g, $return) {
                    $this->assertEquals(new Status(Status::STATE_ONE), $return);
                },
                'value' => Status::STATE_TWO,
                'getter_assert' => function ($g, $return, $v) {
                    $this->assertEquals(new Status(Status::STATE_TWO), $return);
                },
            ]],

            [ 'status', [
                'target' => new StatusAwareEntity(),
                'value' => 'state',
                'setter_exception' => [ 'RuntimeException', 'No status entity' ]
            ]],

            [ 'status', [
                'target' => new StatusAwareEntity(),
                'value' => new Status(Status::STATE_TWO),
                'getter_method' => 'has*',
                'getter_args' => [ Status::STATE_TWO ],
                'expect' => true,
            ]],
        ];
    }
}

class StatusAwareEntity implements StatusAwareEntityInterface
{
    use StatusAwareEntityTrait;
}

class ProvideEntityStatusAwareEntity
{
    use StatusAwareEntityTrait;

    private $statusEntity = Status::class;
}

class Status extends AbstractStatusEntity implements StatusInterface
{
    const STATE_ONE = 'one';
    const STATE_TWO = 'two';

    protected static $orderMap = [
        self::STATE_ONE => 10,
        self::STATE_TWO => 20,
    ];

    protected $default = self::STATE_ONE;
}
