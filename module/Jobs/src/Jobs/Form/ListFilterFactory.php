<?php

namespace Jobs\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class ListFilterFieldsetExtendedFactory implements FactoryInterface
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $auth = $serviceLocator->getServiceLocator()->get('AuthenticationService');
        $fieldset = Null;
        //if ($)
        return $fieldset;
    }

}