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

use Organizations\Factory\Controller\Plugin\InvitationHandlerFactory;

/**
 * Tests for \Organizations\Factory\Controller\Plugin\InvitationHandlerFactory
 * 
 * @covers \Organizations\Factory\Controller\Plugin\InvitationHandlerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Factory
 * @group Organizations.Factory.Controller
 * @group Organizations.Factory.Controller.Plugin
 */
class InvitationHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\FactoryInterface', new InvitationHandlerFactory());
    }

    /**
     * @testdox Creates an InvitationHandler instance and injects the dependencies.
     */
    public function testCreateService()
    {
        $target = new InvitationHandlerFactory();

        $tokenGenerator = $this->getMockBuilder('\Auth\Service\UserUniqueTokenGenerator')
                               ->disableOriginalConstructor()->getMock();

        $userRepository = $this->getMockBuilder('\Auth\Repository\User')->disableOriginalConstructor()->getMock();

        $repositories = $this->getMockBuilder('\Core\Repository\RepositoryService')->disableOriginalConstructor()->getMock();
        $repositories->expects($this->once())->method('get')->with('Auth/User')->willReturn($userRepository);

        $translator = new \Zend\I18n\Translator\Translator();

        $mailer = $this->getMockBuilder('\Core\Controller\Plugin\Mailer')->disableOriginalConstructor()->getMock();

        $emailValidator = new \Zend\Validator\EmailAddress();

        $validators = $this->getMockBuilder('\Zend\Validator\ValidatorPluginManager')->disableOriginalConstructor()->getMock();
        $validators->expects($this->once())->method('get')->with('EmailAddress')->willReturn($emailValidator);

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $services->expects($this->exactly(4))
                 ->method('get')
                 ->will($this->returnValueMap(array(
                     array('ValidatorManager', true, $validators),   // get ha signature ($name, $usePeeringManagers = true)
                     array('translator', true, $translator),
                     array('repositories', true, $repositories),
                     array('Auth/UserTokenGenerator', true, $tokenGenerator)
                 )));

        $plugins = $this->getMockBuilder('\Zend\Mvc\Controller\PluginManager')->disableOriginalConstructor()->getMock();
        $plugins->expects($this->once())->method('getServiceLocator')->willReturn($services);
        $plugins->expects($this->once())->method('get')->with('Mailer')->willReturn($mailer);

        /*
         * test start here
         */
        $plugin = $target->createService($plugins);

        $this->assertInstanceOf('\Organizations\Controller\Plugin\InvitationHandler', $plugin);
        $this->assertSame($tokenGenerator, $plugin->getUserTokenGenerator());
        $this->assertSame($userRepository, $plugin->getUserRepository());
        $this->assertSame($translator, $plugin->getTranslator());
        $this->assertSame($mailer, $plugin->getMailerPlugin());
        $this->assertSame($emailValidator, $plugin->getEmailValidator());

    }
}