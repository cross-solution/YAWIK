<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Core\Factory\Form\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Form\View\Helper\FormEditorLight;


class FormEditorLightFactory implements FactoryInterface {

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FormEditorLight
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $basepath = $serviceLocator->getServiceLocator()->get('ViewHelperManager')->get('basepath');
        
        /* @var \Zend\ServiceManager\AbstractPluginManager $serviceLocator */
        /* @var \Zend\Mvc\MvcEvent $event */
        $event = $serviceLocator->getServiceLocator()->get('application')->getMvcEvent();
         
         
        $lang = $event->getRouteMatch()->getParam('lang');

        $helper = new FormEditorLight();
        $helper->setLanguage($lang);
        $helper->setLanguagePath($basepath('/js/tinymce-lang/'));
        return $helper;
    }


}

