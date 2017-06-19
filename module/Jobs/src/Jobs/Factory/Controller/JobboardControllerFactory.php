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
use Jobs\Controller\JobboardController;
use Jobs\Repository;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JobboardControllerFactory implements FactoryInterface
{
    /**
     * Name of the pagination service
     *
     * @var string $paginationService;
     */
    protected $paginationService = 'Jobs/Board';
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        /* @var \Jobs\Options\JobboardSearchOptions $options */
        $options = $container->get('Jobs/JobboardSearchOptions');

        /**
         * @var $jobRepository Repository\Job
         */
        $jobRepository = $container->get('repositories')->get('Jobs/Job');
	    $defaultListener = $container->get('DefaultListeners');
	    $imageFileCacheManager = $container->get('Organizations\ImageFileCache\Manager');
        return new JobboardController($defaultListener,$jobRepository,$imageFileCacheManager, ['count'=>$options->getPerPage()]);
    }

    /**
     * Injects all needed services into the JobboardController
     *
     * @param ServiceLocatorInterface $services
     *
     * @return JobboardController
     */
    /*public function createService(ServiceLocatorInterface $services)
    {
        return $this($services->getServiceLocator(), JobboardController::class);
    }*/
}
