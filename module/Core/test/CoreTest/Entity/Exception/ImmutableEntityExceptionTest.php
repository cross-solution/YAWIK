<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Entity\Exception;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Tests for \Core\Entity\Exception\ImmutableEntityException
 *
 * @covers \Core\Entity\Exception\ImmutableEntityException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Exception
 */
class ImmutableEntityExceptionTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    protected $target = [ '\Core\Entity\Exception\ImmutableEntityException', [ 'entityClassName' ] ];

    protected $inheritance = [
        '\RuntimeException',
        '\Core\Exception\ExceptionInterface',
        '\Core\Entity\Exception\ExceptionInterface',
    ];

    public function propertiesProvider()
    {
        return [
            [ 'message', [
                'value' => 'entityClassName is an immutable entity.',
                'ignore_setter' => true,
            ]],
            [ 'message', [
                'value' => 'stdClass is an immutable entity.',
                'ignore_setter' => true,
                'target' => ['\Core\Entity\Exception\ImmutableEntityException', [ new \stdClass() ] ]
            ]]
        ];
    }
}
