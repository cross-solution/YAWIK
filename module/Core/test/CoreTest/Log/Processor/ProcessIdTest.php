<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Log\Processor;

use PHPUnit\Framework\TestCase;

use Core\Log\Processor\ProcessId;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\Log\Processor\ProcessorInterface;

/**
 * Tests for \Core\Log\Processor\ProcessId
 *
 * @covers \Core\Log\Processor\ProcessId
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class ProcessIdTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = ProcessId::class;

    private $inheritance = [ ProcessorInterface::class ];

    public function testProcessAddsProcessIdToEventArray()
    {
        $event = [];
        $expect = [
            'pid' => getmypid(),
        ];

        $actual = $this->target->process($event);

        $this->assertEquals($expect, $actual);
    }
}
