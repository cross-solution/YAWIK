<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace CoreTest\Entity\Exception;

use Core\Entity\Exception\NotFoundException;

/**
 * @coversDefaultClass \Core\Entity\Exception\NotFoundException
 */
class NotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__construct
     * @covers ::getId
     */
    public function testNotFoundException()
    {
        $id = 'someId';
        $exception = new NotFoundException($id);
        
        $this->assertSame($id, $exception->getId());
        $this->assertContains($id, $exception->getMessage());
        $this->assertContains('Entity with id', $exception->getMessage());
    }
}
