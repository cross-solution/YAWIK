<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Listener\Events;

use PHPUnit\Framework\TestCase;

use Core\Entity\Image;
use Core\Listener\Events\FileEvent;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Zend\EventManager\Event;

/**
 * Tests for \Core\Listener\Events\FileEvent
 *
 * @covers \Core\Listener\Events\FileEvent
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class FileEventTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = FileEvent::class;

    private $inheritance = [ Event::class ];

    public function propertiesProvider()
    {
        $file = new Image();
        return [
            ['params', ['value' => ['one' => 'two']]],
            ['params', [
                'value' => ['file' => $file, 'one' => 'two'],
                'expect' => ['one' => 'two'],
                'post' => function () use ($file) {
                    $this->assertSame($file, $this->target->getFile());
                },
            ]],
            ['file', $file],
            ['file', [
                'pre' => function () use ($file) {
                    $this->target->setParam('file', $file);
                },
                'ignore_setter' => true,
                'value' => $file
            ]]

        ];
    }
}
