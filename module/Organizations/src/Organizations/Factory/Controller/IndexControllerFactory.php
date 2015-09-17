<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Organizations\Factory\Controller;

use Organizations\Controller\IndexController;
use Organizations\Repository;
use Organizations\Form;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IndexControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IndexController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ControllerManager $serviceLocator */
        $serviceLocator = $serviceLocator->getServiceLocator();

        /**
         * @var Repository\Organization $organizationRepository
         */
        $organizationRepository = $serviceLocator->get('repositories')->get('Organizations/Organization');

        /**
         * @var Form\Organizations $form
         */
        $form = new Form\Organizations(null);
        ;

        return new IndexController($form, $organizationRepository);
    }
}
