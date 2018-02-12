<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Controller;

use Core\Controller\AdminController;
use Core\Controller\AdminControllerEvent;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Core\EventManager\EventManager;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use Interop\Container\ContainerInterface;


/**
 * Tests for \Core\Controller\AdminController
 * 
 * @covers \Core\Controller\AdminController
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @group Core
 * @group Core.Controller
 */
class AdminControllerTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var \Core\Controller\AdminController
     */
    protected $target = '\Core\Controller\AdminController';

    protected $inheritance = [ 'Zend\Mvc\Controller\AbstractActionController' ];

    public function setUp()
    {
	    $events = $this->getMockBuilder(EventManager::class)
	                   ->setMethods(['getEvent', 'trigger'])
	                   ->getMock();
	    $this->target = new AdminController($events);
    }

    public function testIndexAction()
    {
        $events = $this->getMockBuilder(EventManager::class)
            ->setMethods(['getEvent', 'trigger'])
            ->getMock();
	    $target = new AdminController($events);
	    $event = new AdminControllerEvent(AdminControllerEvent::EVENT_DASHBOARD, $target);
	    $event->addViewVariables('test', ['testVar' => 'value']);
        $events->expects($this->once())->method('getEvent')
            ->with(AdminControllerEvent::EVENT_DASHBOARD, $this->identicalTo($target))
            ->willReturn($event);

        $events->expects($this->once())->method('trigger')->with($this->identicalTo($event));


        //$services = $this->getServiceManagerMock([
        //                                             'Core/AdminController/Events' => [
        //                                                 'service' => $events,
        //                                                 'count_get' => 1,
        //                                             ]
		//
        //                                          ]);

        /* @var \Zend\View\Model\ViewModel $child
         * @var \Zend\View\Model\ViewModel $viewModel */
        
        $viewModel = $target->indexAction();

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $viewModel);
        $children = $viewModel->getChildren();
        $child = $children[0];
        $this->assertEquals('test', $child->captureTo());
        $this->assertEquals(['test'], $viewModel->getVariable('widgets'));

        $this->assertCount(1, $children);
    }
}
