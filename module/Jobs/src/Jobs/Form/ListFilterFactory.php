<?php

namespace Jobs\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the job opening search formular
 *
 * @package Jobs\Form
 */
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