<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Service;

use Auth\Repository;
use Auth\Service\ForgotPassword;
use Auth\Service\UserUniqueTokenGenerator;
use Core\Controller\Plugin;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForgotPasswordFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ForgotPassword
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**
         * @var Repository\User          $userRepository
         * @var UserUniqueTokenGenerator $tokenGenerator
         */
        $userRepository = $serviceLocator->get('repositories')->get('Auth/User');
        $tokenGenerator = $serviceLocator->get('Auth\Service\UserUniqueTokenGenerator');
        $loginFilter = $serviceLocator->get('Auth\LoginFilter');
        $config = $serviceLocator->get('Auth/Options');

        return new ForgotPassword($userRepository, $tokenGenerator, $loginFilter, $config);
    }
}
