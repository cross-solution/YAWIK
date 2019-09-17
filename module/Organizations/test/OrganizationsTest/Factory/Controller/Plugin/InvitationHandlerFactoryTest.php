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

use PHPUnit\Framework\TestCase;

use Organizations\Factory\Controller\Plugin\InvitationHandlerFactory;
use Zend\Mvc\Controller\PluginManager;

/**
 * Tests for \Organizations\Factory\Controller\Plugin\InvitationHandlerFactory
 *
 * @covers \Organizations\Factory\Controller\Plugin\InvitationHandlerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @group Organizations
 * @group Organizations.Factory
 * @group Organizations.Factory.Controller
 * @group Organizations.Factory.Controller.Plugin
 */
class InvitationHandlerFactoryTest extends TestCase
{

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', new InvitationHandlerFactory());
    }

    /**
     * @testdox Creates an InvitationHandler instance and injects the dependencies.
     */
    public function testInvokation()
    {
        $target = new InvitationHandlerFactory();

        $tokenGenerator = $this->getMockBuilder('\Auth\Service\UserUniqueTokenGenerator')
                               ->disableOriginalConstructor()->getMock();

        $userRepository = $this->getMockBuilder('\Auth\Repository\User')->disableOriginalConstructor()->getMock();

        $repositories = $this->getMockBuilder('\Core\Repository\RepositoryService')->disableOriginalConstructor()->getMock();
        $repositories->expects($this->once())->method('get')->with('Auth/User')->willReturn($userRepository);

        $translator = new \Zend\I18n\Translator\Translator();
    
        $mailer = $this->getMockBuilder('\Core\Controller\Plugin\Mailer')
                       ->disableOriginalConstructor()
                       ->getMock()
        ;
        
        $pluginManager = $this->getMockBuilder(PluginManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $pluginManager->expects($this->once())
            ->method('get')
            ->with('Core/Mailer')
            ->willReturn($mailer)
        ;

        $emailValidator = new \Zend\Validator\EmailAddress();

        $validators = $this->getMockBuilder('\Zend\Validator\ValidatorPluginManager')->disableOriginalConstructor()->getMock();
        $validators->expects($this->once())->method('get')->with('EmailAddress')->willReturn($emailValidator);

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $services->expects($this->exactly(5))
                 ->method('get')
                 ->will($this->returnValueMap(array(
                     array('ValidatorManager', $validators),   // get ha signature ($name, $usePeeringManagers = true)
                     array('translator', $translator),
                     array('repositories', $repositories),
                     array('Auth/UserTokenGenerator', $tokenGenerator),
                     array('ControllerPluginManager',$pluginManager),
                 )));

        /*
         * test start here
         */
        $plugin = $target->__invoke($services, 'irrelevant');

        $this->assertInstanceOf('\Organizations\Controller\Plugin\InvitationHandler', $plugin);
        $this->assertSame($tokenGenerator, $plugin->getUserTokenGenerator());
        $this->assertSame($userRepository, $plugin->getUserRepository());
        $this->assertSame($translator, $plugin->getTranslator());
        $this->assertSame($mailer, $plugin->getMailerPlugin());
        $this->assertSame($emailValidator, $plugin->getEmailValidator());
    }
}
