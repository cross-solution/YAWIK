<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Factory\Controller\Plugin;

use Auth\AuthenticationService;
use Organizations\Factory\Controller\Plugin\AcceptInvitationHandlerFactory;

/**
 * Tests for \Organizations\Factory\Controller\Plugin\AcceptInvitationHandlerFactory
 * 
 * @covers \Organizations\Factory\Controller\Plugin\AcceptInvitationHandlerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @group Organizations
 * @group Organizations.Factory
 * @group Organizations.Factory.Controller
 * @group Organizations.Factory.Controller.Plugin
 */
class AcceptInvitationHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', new AcceptInvitationHandlerFactory());
    }

    /**
     * @testdox Creates a proper configured AcceptInvitationHandler plugin instance.
     */
    public function testInvokation()
    {
        $userRep = $this->getMockBuilder('\Auth\Repository\User')->disableOriginalConstructor()->getMock();
        $orgRep = $this->getMockBuilder('\Organizations\Repository\Organization')->disableOriginalConstructor()->getMock();

        $repositories = $this->getMockBuilder('\Core\Repository\RepositoryService')->disableOriginalConstructor()->getMock();
        $repositories->expects($this->exactly(2))->method('get')->will($this->returnValueMap(array(
            array('Auth/User', $userRep),
            array('Organizations', $orgRep)
        )));

        $auth = $this->getMockBuilder('\Auth\AuthenticationService')->disableOriginalConstructor()->getMock();

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                         ->disableOriginalConstructor()
                         ->getMock()
        ;
        $services->expects($this->exactly(2))
                 ->method('get')
                 ->will($this->returnValueMap(
		            array(
		                array('repositories',$repositories),
		                array('AuthenticationService', $auth)
		            )
                 ))
        ;

        $target = new AcceptInvitationHandlerFactory();
        /*
         * Test start here
         */

        $plugin = $target->__invoke($services,'irrelevant');
	    
        $this->assertInstanceOf('\Organizations\Controller\Plugin\AcceptInvitationHandler', $plugin);
        $this->assertSame($userRep, $plugin->getUserRepository());
        $this->assertSame($orgRep, $plugin->getOrganizationRepository());
        $this->assertSame($auth, $plugin->getAuthenticationService());
    }

}