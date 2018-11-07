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
use Organizations\Controller\Plugin\InvitationHandler;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for an InvitationHandler.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @since  0.19
 */
class InvitationHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // @TODO: [ZF3] Check if InvitationHandlerFactory still working properly
        
        /* @var $container \Zend\Mvc\Controller\PluginManager */
        $validator  = $container->get('ValidatorManager')->get('EmailAddress');
        $mailer     = $container->get('ControllerPluginManager')->get('Core/Mailer');
        $translator = $container->get('translator');
        $repository = $container->get('repositories')->get('Auth/User');
        $generator  = $container->get('Auth/UserTokenGenerator');

        $plugin = new InvitationHandler();
        $plugin->setEmailValidator($validator)
            ->setMailerPlugin($mailer)
            ->setTranslator($translator)
            ->setUserRepository($repository)
            ->setUserTokenGenerator($generator);

        return $plugin;
    }
}
