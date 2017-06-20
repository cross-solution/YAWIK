<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Organizations\Factory\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Organizations\Controller\Plugin\AcceptInvitationHandler;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for an InvitationHandler.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @since  0.19
 */
class AcceptInvitationHandlerFactory implements FactoryInterface
{

    /**
     * Create a AcceptInvitationHandler controller plugin
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return AcceptInvitationHandler
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repositories           = $container->get('repositories');
        $userRepository         = $repositories->get('Auth/User');
        $organizationRepository = $repositories->get('Organizations');
        $authenticationService  = $container->get('AuthenticationService');

        $plugin = new AcceptInvitationHandler();
        $plugin->setUserRepository($userRepository)
               ->setOrganizationRepository($organizationRepository)
               ->setAuthenticationService($authenticationService);

        return $plugin;
    }
}
