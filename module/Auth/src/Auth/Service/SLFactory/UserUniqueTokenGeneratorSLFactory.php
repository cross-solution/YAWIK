<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Service\SLFactory;

use Auth\Service\UserUniqueTokenGenerator;
use Core\Repository\RepositoryService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserUniqueTokenGeneratorSLFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UserUniqueTokenGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**
         * @var RepositoryService $repositoryService
         */
        $repositoryService = $serviceLocator->get('repositories');

        return new UserUniqueTokenGenerator($repositoryService);
    }
}