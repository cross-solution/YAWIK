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

use CoreTestUtils\TestCase\AssertInheritanceTrait;

/**
 * Tests for \Core\Entity\Exception\OutOfBoundsException
 * 
 * @covers \Core\Entity\Exception\OutOfBoundsException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Exception
 */
class OutOfBoundsExceptionTest extends \PHPUnit_Framework_TestCase
{
    use AssertInheritanceTrait;

    protected $target = '\Core\Entity\Exception\OutOfBoundsException';

    protected $inheritance = [
        '\OutOfBoundsException',
        '\Core\Exception\ExceptionInterface',
        '\Core\Entity\Exception\ExceptionInterface',
    ];
}