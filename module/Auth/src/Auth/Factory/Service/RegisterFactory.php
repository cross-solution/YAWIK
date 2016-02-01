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
use Auth\Service\Register;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegisterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Register
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var Repository\User $userRepository */
        $userRepository = $serviceLocator->get('repositories')->get('Auth/User');

        /* @var \Core\Mail\MailService $mailService */
        $mailService = $serviceLocator->get('Core/MailService');

        /* @var \Core\Options\ModuleOptions $config */
        $config = $serviceLocator->get('Core/Options');

        return new Register($userRepository, $mailService, $config);
    }
}
