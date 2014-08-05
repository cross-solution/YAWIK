<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace AuthTest\Service;

use Auth\Service\HybridAuthFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

require_once(__DIR__ . '/../../../../../vendor/hybridauth/hybridauth/hybridauth/Hybrid/Provider_Model.php');


class HybridAuthFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testFactoryReturnsInstanceOfHybridAuth()
    {
       
        $sm = new ServiceManager();
        
        $sessionManagerMock = $this->getMock('\Zend\SessionManager\SessionManager', array('start'));
        
        $sessionManagerMock->expects($this->once())
            ->method('start');
        $sm->setAllowOverride(true);
        $sm->setService('SessionManager', $sessionManagerMock);
      
        $sm->setFactory('Config', function ($sm) {
            return array(
                'hybridauth' => array(
                    'test' => array(
                        'enable' => false
                    )
                )
            );
        });
        
        $routerMock = $this->getMock('\Zend\Mvc\Router\Http\TreeRouteStack', array('assemble'));
        $routerMock->expects($this->once())
            ->method('assemble')
            ->with(array(), array(
                'name' => 'auth/hauth',
                'force_canonical' => true,
            ))
            ->will($this->returnValue('/login/hauth'));

        $sm->setService('Router', $routerMock);
        
        $_SERVER['HTTP_HOST'] = 'test.cam';
        $_SERVER['REQUEST_URI'] = '/login/test';
        
        $hybridAuthFactory = new HybridAuthFactory();
        $hybridAuth = $hybridAuthFactory->createService($sm);
        
        $this->assertInstanceOf('\Hybrid_Auth', $hybridAuth);
        
        $config = $hybridAuth::$config;
        $this->assertEquals('/login/hauth', $config['base_url']);
        $this->assertEquals(false, $config['providers']['test']['enable']);
        
    }
}




