<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Form\UserStatusFieldset;

class UserStatusFieldsetFactory implements FactoryInterface
{
    /**
     * Create an UserStatusFieldset
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return UserStatusFieldset
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $translator = $container->get('Translator');
        $statusOptions = (new \Auth\Entity\Status())->getOptions($translator);
        $fieldset = new UserStatusFieldset();
        $fieldset->setStatusOptions($statusOptions);

        return $fieldset;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), UserStatusFieldset::class);
    }
}
