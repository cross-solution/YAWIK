<?php

namespace Cv\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Form;

class CvFactory implements FactoryInterface
{
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $form = new Form('create_cv');
        //$form->add($serviceLocator->get('ApplicationFieldset'));
        $form->add($serviceLocator->get('EducationCollection'));
        
        $form->add($serviceLocator->get('DefaultButtonsFieldset'), array('name' => 'buttons'));
        return $form;

    }
}
