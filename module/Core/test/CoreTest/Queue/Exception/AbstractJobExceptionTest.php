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
use Core\Queue\Exception\JobExceptionInterface;
use CoreTestUtils\TestCase\TestInheritanceTrait;

/**
 * Tests for \Core\Queue\Exception\AbstractJobException
 *
 * @covers \Core\Queue\Exception\AbstractJobException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class AbstractJobExceptionTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = [
        AbstractJobException::class,
        'as_reflection' => true,
        '@testConstruction' => false,
    ];

    private $inheritance = [ JobExceptionInterface::class, \RuntimeException::class ];

    public function provideConstructionTestData()
    {
        return [
            [null, null],
            ['Meaningful error message', null],
            ['I will be overridden', ['message' => 'I will overwrite']],
        ];
    }

    /**
     * @dataProvider provideConstructionTestData
     *
     * @param $message
     * @param $options
     */
    public function testConstruction($message, $options)
    {
        $args = [$message];
        if (null !== $options) {
            $args[] = $options;
        }
        $target = new ConcreteAbstractJobException(...$args);

        $additionalOptions = [
            'message' => $message,
            'trace' => $target->getTraceAsString()
        ];

        $expectOptions = null === $options ? $additionalOptions : array_merge($additionalOptions, $options);

        $this->assertEquals($expectOptions, $target->getOptions());
    }
}

class ConcreteAbstractJobException extends AbstractJobException
{
}
