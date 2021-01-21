<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Applications\Factory\Mail;

use Applications\Mail\Confirmation;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Applications\Mail\Confirmation
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @todo write test
 */
class ConfirmationFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $router = $container->get('Router');
        $options['router'] = $router;
        $auth = $container->get('AuthenticationService');
        $user = $auth->getUser();
        $options['user'] = $user;
        $mail   = new Confirmation($options);

        return $mail;
    }
}
