<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Organizations\Factory\Controller\Plugin;

use Organizations\Controller\Plugin\InvitationHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for an InvitationHandler.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.19
 */
class InvitationHandlerFactory implements FactoryInterface
{
    /**
     * Creates an InvitationHandler
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return InvitationHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\Mvc\Controller\PluginManager */
        $services   = $serviceLocator->getServiceLocator();
        $validator  = $services->get('ValidatorManager')->get('EmailAddress');
        $mailer     = $serviceLocator->get('Mailer');
        $translator = $services->get('translator');
        $repository = $services->get('repositories')->get('Auth/User');
        $generator  = $services->get('Auth/UserTokenGenerator');

        $plugin = new InvitationHandler();
        $plugin->setEmailValidator($validator)
               ->setMailerPlugin($mailer)
               ->setTranslator($translator)
               ->setUserRepository($repository)
               ->setUserTokenGenerator($generator);

        return $plugin;
    }
}
