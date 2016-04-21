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

use Core\Controller\AdminControllerEvent;
use CoreTestUtils\TestCase\AssertInheritanceTrait;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;


/**
 * Tests for \Core\Controller\AdminController
 * 
 * @covers \Core\Controller\AdminController
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Controller
 */
class AdminControllerTest extends \PHPUnit_Framework_TestCase
{
    use AssertInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var \Core\Controller\AdminController
     */
    protected $target = '\Core\Controller\AdminController';

    protected $inheritance = [ 'Zend\Mvc\Controller\AbstractActionController' ];

    public function testIndexAction()
    {
        $event = new AdminControllerEvent(AdminControllerEvent::EVENT_DASHBOARD, $this->target);
        $event->addViewVariables('test', ['testVar' => 'value']);

        $events = $this->getMock('\Core\EventManager\EventManager', ['getEvent', 'trigger']);
        $events->expects($this->once())->method('getEvent')
            ->with(AdminControllerEvent::EVENT_DASHBOARD, $this->identicalTo($this->target))
            ->willReturn($event);

        $events->expects($this->once())->method('trigger')->with($this->identicalTo($event));


        $services = $this->getServiceManagerMock([
                                                     'Core/AdminController/Events' => $events,
                                                 ]);

        /* @var \Zend\View\Model\ViewModel $child
         * @var \Zend\View\Model\ViewModel $viewModel */
        $this->target->setServiceLocator($services);
        $viewModel = $this->target->indexAction();

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $viewModel);
        $children = $viewModel->getChildren();
        $child = $children[0];
        $this->assertEquals('test', $child->captureTo());
        $this->assertEquals(['test'], $viewModel->getVariable('widgets'));

        $this->assertCount(1, $children);
    }
}