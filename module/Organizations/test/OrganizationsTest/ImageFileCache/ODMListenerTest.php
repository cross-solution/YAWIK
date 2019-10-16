<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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
    
    public function testPreUpdateWithNonOrganizationEntity()
    {
        $event = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $event->expects($this->once())
            ->method('getDocument')
            ->willReturn(new stdClass());
        
        $event->expects($this->never())
            ->method('hasChangedField');
        
        $this->listener->preUpdate($event);
        $this->assertAttributeEmpty('delete', $this->listener);
    }
    
    public function testPreUpdateWithNonUnchangedImage()
    {
        $event = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $event->expects($this->once())
            ->method('getDocument')
            ->willReturn(new Organization());
        
        $event->expects($this->once())
            ->method('hasChangedField')
            ->with($this->equalTo('image'))
            ->willReturn(false);
        
        $event->expects($this->never())
            ->method('getOldValue');
        
        $this->listener->preUpdate($event);
        $this->assertAttributeEmpty('delete', $this->listener);
    }
    
    public function testPreUpdateWithChangedInvalidImage()
    {
        $event = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $event->expects($this->once())
            ->method('getDocument')
            ->willReturn(new Organization());
        
        $event->expects($this->once())
            ->method('hasChangedField')
            ->with($this->equalTo('image'))
            ->willReturn(true);
        
        $event->expects($this->once())
            ->method('getOldValue')
            ->willReturn(new stdClass());
        
        $this->listener->preUpdate($event);
        $this->assertAttributeEmpty('delete', $this->listener);
    }

    public function testPreUpdateWithValidImage()
    {
        $event = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $event->expects($this->once())
            ->method('getDocument')
            ->willReturn(new Organization());
        
        $event->expects($this->once())
            ->method('hasChangedField')
            ->with($this->equalTo('image'))
            ->willReturn(true);
        
        $ImageEntity = new ImageEntity();
        $event->expects($this->once())
            ->method('getOldValue')
            ->willReturn($ImageEntity);
        
        $this->listener->preUpdate($event);
        $this->assertAttributeNotEmpty('delete', $this->listener);
        $this->assertAttributeContains($ImageEntity, 'delete', $this->listener);
        
        return [$this->listener, $this->manager, $ImageEntity];
    }

    public function testPostFlushWithoutImage()
    {
        $event = $this->getMockBuilder(PostFlushEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->manager->expects($this->never())
            ->method('delete');
        
        $this->listener->postFlush($event);
    }
    
    /**
     * @param array $parameters
     * @depends testPreUpdateWithValidImage
     */
    public function testPostFlushWithImage(array $parameters)
    {
        list($listener, $manager, $ImageEntity) = $parameters;

        $event = $this->getMockBuilder(PostFlushEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $manager->expects($this->once())
            ->method('delete')
            ->with($this->identicalTo($ImageEntity));

        $listener->postFlush($event);
    }
}
