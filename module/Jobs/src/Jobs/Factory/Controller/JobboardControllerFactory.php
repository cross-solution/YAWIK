<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Jobs\Factory\Controller;

use Jobs\Controller\JobboardController;
use Jobs\Repository;
use Zend\Form\Form;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JobboardControllerFactory implements FactoryInterface
{
    /**
     * Name of the pagination service
     *
     * @var string $paginationService;
     */
    protected $paginationService = 'Jobs/Board';

    /**
     * Injects all needed services into the JobboardController
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return JobboardController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ControllerManager $serviceLocator */
        $serviceLocator = $serviceLocator->getServiceLocator();

//        $searchForm = $serviceLocator->get('forms')
//            ->get('Jobs/ListFilterLocation');

        /**
         * @var $jobRepository Repository\Job
         */
        $jobRepository = $serviceLocator->get('repositories')->get('Jobs/Job');

        return new JobboardController($jobRepository);
    }
}
