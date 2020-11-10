<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UniqueApplyIdFactory.php */
namespace Auth\Form\Validator;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for UniqueGroupName validator.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class UniqueLoginNameFactory implements FactoryInterface
{
    public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
    {
        $repository = $container->get('repositories')->get('Auth/User');
        $user       = $container->get('AuthenticationService')->getUser();
        $validator  = new UniqueLoginName($options);

        $validator->setUserRepository($repository);
        $validator->setCurrentUser($user);

        return $validator;
    }
}
