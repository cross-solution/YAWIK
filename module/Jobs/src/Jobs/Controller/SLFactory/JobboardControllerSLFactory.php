<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Jobs\Controller\SLFactory;

use Jobs\Controller\JobboardController;
use Jobs\Repository;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JobboardControllerSLFactory implements FactoryInterface
{

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

        $searchForm = $serviceLocator->get('forms')->get('Jobs/ListFilter', /* useAcl */ false);

        /**
         * @var $jobRepository Repository\Job
         */
        $jobRepository = $serviceLocator->get('repositories')->get('Jobs/Job');

        return new JobboardController( $jobRepository, $searchForm );
    }
}