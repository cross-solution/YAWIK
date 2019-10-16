<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace AuthTest\Dependency;

use PHPUnit\Framework\TestCase;

use Auth\Dependency\Manager;
use Zend\EventManager\EventManagerInterface as Events;
use Auth\Entity\UserInterface as User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Zend\EventManager\EventManager;
use Auth\Dependency\ListInterface;

/**
 * @coversDefaultClass \Auth\Dependency\Manager
 */
class ManagerTest extends TestCase
{
    /**
     * @var Manager
     */
    private $manager;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $events;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $documentManager;
    
    /**
     * @see PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->events = $this->getMockBuilder(Events::class)
            ->getMock();
        
        $this->documentManager = $this->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->manager = new Manager($this->documentManager);
        $this->manager->setEventManager($this->events);
    }
    
    /**
     * @covers ::__construct
     * @covers ::getLists
     */
    public function testGetLists()
    {
        $return = [];
        
        $this->events->expects($this->once())
            ->method('trigger')
            ->with($this->equalTo(Manager::EVENT_GET_LISTS), $this->equalTo($this->manager))
            ->willReturn($return);
        
        $this->assertSame($return, $this->manager->getLists());
    }

    public function removeItemsTestDataProvider()
    {
        return [
            ['onTrigger'], ['onFlush'], ['no']
        ];
    }

    /**
     * @dataProvider removeItemsTestDataProvider
     * @covers ::removeItems
     */
    public function testRemoveItems($throwException)
    {
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        
        $triggerStub = $this->events->expects($this->once())
            ->method('trigger')
            ->with($this->equalTo(Manager::EVENT_REMOVE_ITEMS), $this->equalTo($this->manager), $this->equalTo(['user' => $user]));

        if ('onTrigger' == $throwException) {
            $triggerStub->will($this->throwException(new \Exception));
            $expects = false;
            $flushStub = $this->documentManager->expects($this->never())->method('flush');
        } elseif ('onFlush' == $throwException) {
            $this->documentManager->expects($this->once())->method('flush')->will($this->throwException(new \Exception));
            $expects = false;
        } else {
            $this->documentManager->expects($this->once())->method('flush');
            $expects = true;
        }

        $this->assertSame($expects, $this->manager->removeItems($user));
    }
    
    /**
     * @covers ::attachDefaultListeners
     */
    public function testAttachDefaultListeners()
    {
        $this->events->expects($this->once())
            ->method('attach')
            ->with($this->equalTo(Manager::EVENT_REMOVE_ITEMS));
        
        $this->manager->setEventManager($this->events);
    }
    
    /**
     * @covers ::attachDefaultListeners
     */
    public function testDefaultListenersAreCalled()
    {
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        
        $this->events = new EventManager();
        
        $item = new \stdClass();
        $items = [$item];
        
        $list = $this->getMockBuilder(ListInterface::class)
            ->getMock();
        $list->expects($this->once())
            ->method('getEntities')
            ->with($this->equalTo($user))
            ->willReturn($items);
        
        $this->manager = $this->getMockBuilder(Manager::class)
            ->setConstructorArgs([$this->documentManager])
            ->setMethods(['getLists'])
            ->getMock();
        $this->manager->expects($this->once())
            ->method('getLists')
            ->willReturn([$list]);
        $this->manager->setEventManager($this->events);
        
        $this->documentManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($item));
        
        $this->assertTrue($this->manager->removeItems($user));
    }
}
