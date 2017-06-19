<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Service;

use Auth\Factory\Adapter\HybridAuthAdapterFactory;

/**
 * Class HybridAuthAdapterFactoryTest
 * @package AuthTest\Factory\Service
 * @covers \Auth\Factory\Adapter\HybridAuthAdapterFactory
 */
class HybridAuthAdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HybridAuthAdapterFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new HybridAuthAdapterFactory();
    }

    public function testCreateService()
    {
        $hybridAuthMock = $this->getMockBuilder('Hybrid_Auth')
            ->disableOriginalConstructor()
            ->getMock();

        $userRepositoryMock = $this->getMockBuilder('Auth\Repository\User')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock->expects($this->once())
            ->method('get')
            ->with('Auth/User')
            ->willReturn($userRepositoryMock);
        
        $socialProfilesPluginMock = $this->getMockBuilder('Auth\Controller\Plugin\SocialProfiles')
            ->disableOriginalConstructor()
            ->getMock();
        
        $contollerPluginsMock = $this->getMockBuilder('Zend\Mvc\Controller\PluginManager')
            ->disableOriginalConstructor()
            ->getMock();
        
        $contollerPluginsMock->expects($this->once())
            ->method('get')
            ->with('Auth/SocialProfiles')
            ->willReturn($socialProfilesPluginMock);

        $sm = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();

        $sm->expects($this->exactly(3))
            ->method('get')
            ->will($this->returnValueMap(
                array(
                    array('HybridAuth', $hybridAuthMock),
                    array('repositories', $repositoriesMock),
                    array('ControllerPluginManager', $contollerPluginsMock)
                )
            ));
	    
        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Auth\Adapter\HybridAuth', $result);
    }
}
