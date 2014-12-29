<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Service\SLFactory;

use Auth\Repository;
use Auth\Service\ForgotPassword;
use Auth\Service\UserUniqueTokenGenerator;
use Core\Controller\Plugin;
use Zend\Log\LoggerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForgotPasswordSLFactory implements FactoryInterface
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

        return new ForgotPassword($userRepository, $tokenGenerator);
    }
}