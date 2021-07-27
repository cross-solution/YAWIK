<?php
/**
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */

namespace OrganizationsTest\ImageFileCache;

use PHPUnit\Framework\TestCase;

use Organizations\ImageFileCache\ODMListener;
use Organizations\ImageFileCache\Manager;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationImage as ImageEntity;
use stdClass;

/**
 * @coversDefaultClass \Organizations\ImageFileCache\ODMListener
 */
class ODMListenerTest extends TestCase
{

    /**
     * @var ODMListener
     */
    protected $listener;

    /**
     * @var Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->listener = new ODMListener($this->manager);
    }


    /**
     * @param bool $enabled
     * @param array $expected
     * @dataProvider dataGetSubscribedEvents
     */
    public function testGetSubscribedEvents($enabled, array $expected)
    {
        $this->manager->expects($this->once())
            ->method('isEnabled')
            ->willReturn($enabled);

        $this->assertEquals($expected, $this->listener->getSubscribedEvents());
    }

    /**
     * @return array
     */
    public function dataGetSubscribedEvents()
    {
        return [
            [false, []],
            [true, [Events::preUpdate, Events::postFlush]]
        ];
    }
}
