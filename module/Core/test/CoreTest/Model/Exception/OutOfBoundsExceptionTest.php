<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace CoreTest\Model\Exception;

class OutOfBoundsExceptionTest extends \PHPUnit_Framework_TestCase
{
    
    public function testExceptionImplementsExceptionInterface()
    {
        $e = new \Core\Model\Exception\OutOfBoundsException();
        $this->assertInstanceOf('\Core\Model\Exception\ExceptionInterface', $e);
    }
    
    public function testExceptionExtendsSplOutOfBoundsException()
    {
        $e = new \Core\Model\Exception\OutOfBoundsException();
        $this->assertInstanceOf('\OutOfBoundsException', $e);
    }
    
}