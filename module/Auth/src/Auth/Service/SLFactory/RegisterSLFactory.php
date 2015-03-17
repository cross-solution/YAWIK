<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Service\SLFactory;

use Auth\Repository;
use Auth\Service\Register;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Options\ServiceRegisterOptions;

class RegisterSLFactory implements FactoryInterface
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
        /**
         * @var Repository\User $userRepository
         */
        $userRepository = $serviceLocator->get('repositories')->get('Auth/User');
        $mailService = $serviceLocator->get('Core/MailService');

        $serviceRegisterOptions = new ServiceRegisterOptions(array('userRepository' => $userRepository, 'mailService' => $mailService));

        return new Register($serviceRegisterOptions);
    }
}