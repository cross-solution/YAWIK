<?php

namespace Jobs\Factory\Form;

use Interop\Container\ContainerInterface;
use Jobs\Form\CompanyNameFieldset;
use Jobs\Form\Hydrator\OrganizationNameHydrator;
use Zend\Form\FormElementManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CompanyNameFieldsetFactory implements FactoryInterface
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
        /**
         * @var $hydrator OrganizationNameHydrator
         */
        $hydrator = $container->get('Jobs\Form\Hydrator\OrganizationNameHydrator');
        $form = new CompanyNameFieldset();
        $form->setHydrator($hydrator);

        return $form;
    }


    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CompanyNameFieldset
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), CompanyNameFieldset::class);
    }
}
