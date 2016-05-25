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

use Core\Entity\Exception\ImmutableEntityException;
use CoreTestUtils\TestCase\AssertInheritanceTrait;
use CoreTestUtils\TestCase\SetterGetterTrait;

/**
 * Tests for \Core\Entity\Exception\ImmutableEntityException
 * 
 * @covers \Core\Entity\Exception\ImmutableEntityException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Exception
 */
class ImmutableEntityExceptionTest extends \PHPUnit_Framework_TestCase
{
    use AssertInheritanceTrait, SetterGetterTrait;

    protected $target = [ 'class' => '\Core\Entity\Exception\ImmutableEntityException', 'args' => [ 'entityClassName' ] ];

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