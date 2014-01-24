<?php

namespace Applications\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Repository\Hydrator\FileUploadStrategy;
use Core\Entity\Hydrator\EntityHydrator;
use Auth\Form\UserInfoFieldset;

class ContactFieldsetFactory implements FactoryInterface
{
    
    protected $imageMetaData = array();
    
    public function __construct(array $options = array()) {
        if (isset($options['image_meta'])) {
            $this->imageMetaData = $options['image_meta'];
        }
        
    }
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services     = $serviceLocator->getServiceLocator();
       # $repositories = $services->get('repositories');
        #$repository   = $repositories->get('Applications/Files');
        $meta         = $this->imageMetaData;
        $auth         = $services->get('AuthenticationService');
        if ($auth->hasIdentity()) {
            $meta['user'] = $auth->getIdentity();
        }
        $fieldset     = new UserInfoFieldset();
       # $strategy     = new FileUploadStrategy($repository, $meta);
        $hydrator     = new EntityHydrator();
      #  $hydrator->addStrategy('image', $strategy);
        $fieldset->setHydrator($hydrator);

        return $fieldset;
    }
}