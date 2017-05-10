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
use Core\Entity\Hydrator\Strategy\FileUploadStrategy;
use Core\Entity\Hydrator\EntityHydrator;
use Auth\Entity\UserImage;
use Auth\Form\UserInfoFieldset;

class UserInfoFieldsetFactory implements FactoryInterface
{
    /**
     * Create an UserInfoFieldset.
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return UserInfoFieldset
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $user = $container->get('AuthenticationService')->getUser();
        $fieldset     = new UserInfoFieldset();
        $imageEntity  = new UserImage();
        $imageEntity->setUser($user);
        $strategy     = new FileUploadStrategy($imageEntity);
        $hydrator     = new EntityHydrator();
        $hydrator->addStrategy('image', $strategy);
        $fieldset->setHydrator($hydrator);
        return $fieldset;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), UserInfoFieldset::class);
    }
}
