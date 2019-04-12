<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace CoreTest\Entity\Exception;

use PHPUnit\Framework\TestCase;

use Core\Entity\Exception\NotFoundException;

/**
 * @coversDefaultClass \Core\Entity\Exception\NotFoundException
 */
class NotFoundExceptionTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::getId
     */
    public function testNotFoundException()
    {
        $id = 'someId';
        $exception = new NotFoundException($id);
        
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(\Core\Entity\Exception\ExceptionInterface::class, $exception);
        $this->assertSame($id, $exception->getId());
        $this->assertContains($id, $exception->getMessage());
        $this->assertContains('Entity with id', $exception->getMessage());
    }
}
