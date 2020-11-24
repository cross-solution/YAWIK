<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Queue\Exception;

use PHPUnit\Framework\TestCase;

use Core\Queue\Exception\AbstractJobException;
use Core\Queue\Exception\RecoverableJobException;
use CoreTestUtils\TestCase\TestInheritanceTrait;

/**
 * Tests for \Core\Queue\Exception\RecoverableJobException
 *
 * @covers \Core\Queue\Exception\RecoverableJobException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class RecoverableJobExceptionTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = RecoverableJobException::class;

    private $inheritance = [ AbstractJobException::class ];
}
