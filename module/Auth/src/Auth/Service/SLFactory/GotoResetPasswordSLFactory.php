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
use Auth\Service\ForgotPassword;
use Auth\Service\GotoResetPassword;
use Core\Controller\Plugin;
use Core\Repository\RepositoryService;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GotoResetPasswordSLFactory implements FactoryInterface
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
         * @var RepositoryService $repositoryService
         */
        $repositoryService = $serviceLocator->get('repositories');
        $authenticationService = new AuthenticationService();

        return new GotoResetPassword($repositoryService, $authenticationService);
    }
}