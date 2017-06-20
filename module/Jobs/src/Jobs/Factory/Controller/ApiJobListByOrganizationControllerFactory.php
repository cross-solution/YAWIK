<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

namespace Jobs\Factory\Controller;

use Interop\Container\ContainerInterface;
use Jobs\Controller\ApiJobListByOrganizationController;
use Zend\ServiceManager\Factory\FactoryInterface;

class ApiJobListByOrganizationControllerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repositories = $container->get('repositories');

        /** @var \Jobs\Repository\Job $jobRepository */
        $jobRepository = $repositories->get('Jobs');

        /** @var \Jobs\Model\ApiJobDehydrator $apiJobDehydrator */
        $apiJobDehydrator = $container->get('Jobs\Model\ApiJobDehydrator');

        $controller = new ApiJobListByOrganizationController($jobRepository, $apiJobDehydrator);

        return $controller;
    }
}
