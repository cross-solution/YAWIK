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
use Jobs\Controller\ManageController;
use Core\Repository\RepositoryService;
use Zend\ServiceManager\Factory\FactoryInterface;

class ManageControllerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $auth = $container->get('AuthenticationService');
        /* @var RepositoryService     $repositoryService */
        $repositoryService =    $container->get('repositories');
        $translator =    $container->get('translator');
        $filterManager = $container->get('FilterManager');
        $jobFormEvents = $container->get('Jobs/JobContainer/Events');
        $formManager = $container->get('FormElementManager');
        $options['core'] = $container->get('Core/Options');
        $options['channels'] = $container->get('Jobs/Options/Provider');
        $viewHelper = $container->get('ViewHelperManager');
        $validatorManager = $container->get('ValidatorManager');
        $jobEvents = $container->get('Jobs/Events');
        $jobEvent = $container->get('Jobs/Event');
        return new ManageController(
        	$auth,
	        $repositoryService,
	        $translator,
	        $filterManager,
	        $jobFormEvents,
	        $formManager,
	        $options,
	        $viewHelper,
	        $validatorManager,
	        $jobEvents,
	        $jobEvent
        );
    }
}
