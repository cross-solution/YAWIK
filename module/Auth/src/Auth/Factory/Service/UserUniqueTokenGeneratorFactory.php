<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Service;

use Auth\Service\UserUniqueTokenGenerator;
use Core\Repository\RepositoryService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserUniqueTokenGeneratorFactory implements FactoryInterface
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
