<?php

namespace Auth\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Element\Select;

class RoleSelectFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services   = $serviceLocator->getServiceLocator();
        $config     = $services->get('Config');
        $translator = $services->get('translator');
         
        $publicRoles = isset($config['acl']['public_roles']) 
                       && is_array($config['acl']['public_roles'])
                       && !empty($config['acl']['public_roles'])
                       ? $config['acl']['public_roles']
                       : (in_array('user', $config['acl']['roles']) 
                          || array_key_exists('user', $config['acl']['roles'])
                          ? array('user')
                          : array('none')
                         );
        
        $valueOptions = array(); 
        foreach ($publicRoles as $role) {
            $valueOptions[$role] = $translator->translate($role);
        }
        
        $select = new Select('role');
        $select->setValueOptions($valueOptions);
        
        return $select;
    }
}