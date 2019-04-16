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

/**
 * Tests for \Core\Entity\Exception\OutOfBoundsException
 *
 * @covers \Core\Entity\Exception\OutOfBoundsException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Exception
 */
class OutOfBoundsExceptionTest extends TestCase
{
    use TestInheritanceTrait;

    protected $target = '\Core\Entity\Exception\OutOfBoundsException';

    protected $inheritance = [
        '\OutOfBoundsException',
        '\Core\Exception\ExceptionInterface',
        '\Core\Entity\Exception\ExceptionInterface',
    ];
}
