<?php

namespace Applications\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\Form\Form;

class ApplicationFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $form = new Form('create_application');
        $form->add($serviceLocator->get('ApplicationFieldset'));
        return $form;
        
    }

    
}