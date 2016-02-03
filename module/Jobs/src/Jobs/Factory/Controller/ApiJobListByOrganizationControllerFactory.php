<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

namespace Jobs\Factory\Controller;

use Jobs\Controller\ApiJobListByOrganizationController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApiJobListByOrganizationControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApiJobListByOrganizationController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\Mvc\Controller\PluginManager */
        $services = $serviceLocator->getServiceLocator();
        $repositories = $services->get('repositories');

        /** @var \Jobs\Repository\Job $jobRepository */
        $jobRepository = $repositories->get('Jobs');

        /** @var \Jobs\Model\ApiJobDehydrator $apiJobDehydrator */
        $apiJobDehydrator = $services->get('Jobs\Model\ApiJobDehydrator');

        $controller = new ApiJobListByOrganizationController($jobRepository, $apiJobDehydrator);

        return $controller;
    }
}