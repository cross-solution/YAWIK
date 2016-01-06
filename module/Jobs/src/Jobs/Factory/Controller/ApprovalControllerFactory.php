<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Jobs\Factory\Controller;

use Jobs\Controller\ApprovalController;
use Jobs\Repository;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApprovalControllerFactory implements FactoryInterface
{

    /**
     * Injects all needed services into the IndexController
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApprovalController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var ControllerManager $serviceLocator */
        $service = $serviceLocator->getServiceLocator();

        $searchForm = $service->get('forms')
            ->get('Jobs/ListFilter', [ 'fieldset' => 'Jobs/ListFilterAdminFieldset' ]);

        /* @var $user \Auth\Entity\User */
         $user = $service->get('AuthenticationService')->getUser();

        /* @var $jobRepository Repository\Job */
        $jobRepository = $service->get('repositories')->get('Jobs/Job');

        return new ApprovalController($jobRepository, $searchForm, $user);
    }
}
