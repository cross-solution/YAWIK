<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */


namespace AuthTest\View;

use PHPUnit\Framework\TestCase;

use Auth\View\InjectLoginInfoListener as Listener;
use Zend\View\Model\ViewModel;

class InjectLoginInfoListenerTest extends TestCase
{
    public function testListenerAttachsViewModelWithProperConfiguration()
    {
        $listener = new Listener();
        $viewModel = new ViewModel();
        $e = $this->getMockBuilder('\Zend\Mvc\MvcEvent')->getMock();
        $e->expects($this->once())
            ->method('getViewModel')
            ->will($this->returnValue($viewModel));
        
        $listener->injectLoginInfo($e);
        
        $this->assertTrue($viewModel->hasChildren());
        
        $children = $viewModel->getChildren();
        $this->assertEquals(1, count($children));
        $view = $children[0];
        $this->assertEquals('loginInfo', $view->captureTo());
        $this->assertEquals('auth/index/login-info', $view->getTemplate());
    }
}
