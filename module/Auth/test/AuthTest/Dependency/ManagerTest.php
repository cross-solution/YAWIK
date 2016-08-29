<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace AuthTest\Dependency;

use Auth\Dependency\Manager;
use Zend\EventManager\EventManagerInterface as Events;
use Zend\EventManager\ResponseCollection;
use Auth\Entity\UserInterface as User;

/**
 * @coversDefaultClass \Auth\Dependency\Manager
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
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
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->events = $this->getMockBuilder(Events::class)
            ->getMock();
        
        $this->manager = new Manager();
        $this->manager->setEventManager($this->events);
    }
    
    /**
     * @covers ::getLists
     */
    public function testGetLists()
    {
        $return = new ResponseCollection();
        
        $this->events->expects($this->once())
            ->method('trigger')
            ->with($this->equalTo(Manager::EVENT_GET_LISTS), $this->equalTo($this->manager))
            ->willReturn($return);
        
        $this->assertSame($return, $this->manager->getLists());
    }
    
    /**
     * @covers ::removeItems
     */
    public function testRemoveItems()
    {
        $return = new ResponseCollection();
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        
        $this->events->expects($this->once())
            ->method('trigger')
            ->with($this->equalTo(Manager::EVENT_REMOVE_ITEMS), $this->equalTo($this->manager), $this->equalTo(['user' => $user]))
            ->willReturn($return);
        
        $this->assertSame($return, $this->manager->removeItems($user));
    }
}
