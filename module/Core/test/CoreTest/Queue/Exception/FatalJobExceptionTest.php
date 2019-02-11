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

use Core\Queue\Exception\AbstractJobException;
use Core\Queue\Exception\FatalJobException;
use CoreTestUtils\TestCase\TestInheritanceTrait;

/**
 * Tests for \Core\Queue\Exception\FatalJobException
 * 
 * @covers \Core\Queue\Exception\FatalJobException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *  
 */
class FatalJobExceptionTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    private $target = FatalJobException::class;

    private $inheritance = [ AbstractJobException::class ];
}
