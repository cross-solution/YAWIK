<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */
namespace Solr\Factory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Console\ProgressBar;

class ConsoleControllerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UsersController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator ServiceLocatorInterface */
        $serviceLocator = $serviceLocator->getServiceLocator();
        $manager = $serviceLocator->get('Solr/Manager');
        $client = $manager->getClient($manager->getOptions()->getJobsPath());
        $jobRepository = $serviceLocator->get('repositories')->get('Jobs/Job');
        $progressBarFactory = function ($count, $persistenceNamespace = null) {
            return new ProgressBar($count, $persistenceNamespace);
        };
        
        return new \Solr\Controller\ConsoleController($client, $jobRepository, $progressBarFactory);
    }
}
