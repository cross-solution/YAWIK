<?php

namespace Auth\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Element\Select;
use Core\Repository\Hydrator\FileUploadStrategy;
use Core\Entity\Hydrator\EntityHydrator;

class UserInfoFieldsetFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services     = $serviceLocator->getServiceLocator();
        $repositories = $services->get('repositories');
        $repository   = $repositories->get('Users/Files');
        $user         = $services->get('AuthenticationService')->getUser();
        $fieldset     = new UserInfoFieldset();
        $strategy     = new FileUploadStrategy($repository, array(
            'allowedUserIds' => array($user->getId()),
            'user' => $user->getId()
        ));
        $hydrator     = new EntityHydrator();
        $hydrator->addStrategy('image', $strategy);
        $fieldset->setHydrator($hydrator);

        return $fieldset;
    }
}