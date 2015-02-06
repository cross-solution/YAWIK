<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
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