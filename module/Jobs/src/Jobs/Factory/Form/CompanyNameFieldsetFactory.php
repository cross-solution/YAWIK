<?php

namespace Jobs\Factory\Form;

use Jobs\Form\CompanyNameFieldset;
use Jobs\Form\Hydrator\OrganizationNameHydrator;
use Zend\Form\FormElementManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CompanyNameFieldsetFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CompanyNameFieldset
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var FormElementManager $serviceLocator */
        $serviceLocator = $serviceLocator->getServiceLocator();

        /**
         * @var $hydrator OrganizationNameHydrator
         */
        $hydrator = $serviceLocator->get('Jobs\Form\Hydrator\OrganizationNameHydrator');

        $form = new CompanyNameFieldset();
        $form->setHydrator($hydrator);

        return $form;
    }
}