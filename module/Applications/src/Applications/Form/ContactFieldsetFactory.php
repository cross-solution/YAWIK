<?php

namespace Applications\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\Hydrator\Strategy\FileUploadStrategy;
use Core\Entity\Hydrator\EntityHydrator;
use Auth\Form\UserInfoFieldset;
use Applications\Entity\Attachment;

class ContactFieldsetFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services     = $serviceLocator->getServiceLocator();
       # $repositories = $services->get('repositories');
        #$repository   = $repositories->get('Applications/Files');
        
        $auth         = $services->get('AuthenticationService');
        $contactImage = new Attachment();
        if ($auth->hasIdentity()) {
            $contactImage->setUser($auth->getUser());
        }

        $fieldset     = new UserInfoFieldset();
        $strategy     = new FileUploadStrategy($contactImage);
        $hydrator     = new EntityHydrator();
        $hydrator->addStrategy('image', $strategy);
        $fieldset->setHydrator($hydrator);

        return $fieldset;
    }
}