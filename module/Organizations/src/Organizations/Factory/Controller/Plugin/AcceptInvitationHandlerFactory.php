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

use Organizations\Controller\Plugin\AcceptInvitationHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for an InvitationHandler.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.19
 */
class AcceptInvitationHandlerFactory implements FactoryInterface
{
    /**
     * Creates an AcceptInvitationHandler
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AcceptInvitationHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\Mvc\Controller\PluginManager */
        $services               = $serviceLocator->getServiceLocator();
        $repositories           = $services->get('repositories');
        $userRepository         = $repositories->get('Auth/User');
        $organizationRepository = $repositories->get('Organizations');
        $authenticationService  = $services->get('AuthenticationService');

        $plugin = new AcceptInvitationHandler();
        $plugin->setUserRepository($userRepository)
               ->setOrganizationRepository($organizationRepository)
               ->setAuthenticationService($authenticationService);

        return $plugin;
    }
}
