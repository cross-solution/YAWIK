<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Jobs\Factory\Controller;

use Interop\Container\ContainerInterface;
use Jobs\Controller\ApprovalController;
use Jobs\Repository;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApprovalControllerFactory implements FactoryInterface
{

    /**
     * Create an ApprovalController
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return ApprovalController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $searchForm = $container->get('forms')
                                ->get('Jobs/ListFilterAdmin');

        /* @var $jobRepository Repository\Job */
        $jobRepository = $container->get('repositories')->get('Jobs/Job');

        return new ApprovalController($jobRepository, $searchForm);
    }
}
