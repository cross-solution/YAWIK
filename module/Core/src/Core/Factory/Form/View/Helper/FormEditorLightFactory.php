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

/**
 * Class FormEditorLightFactory
 *
 * @package Core\Factory\Form\View\Helper
 */
class FormEditorLightFactory implements FactoryInterface {

    /**
     * Creates a formular editor instance
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FormEditorLight
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager */
        $services = $serviceLocator->getServiceLocator();
        $basepath = $services->get('ViewHelperManager')->get('basepath');
        $config = $services->get('Config');

        /* @var \Zend\ServiceManager\AbstractPluginManager $serviceLocator */
        /* @var \Zend\Mvc\MvcEvent $event */
        $event = $serviceLocator->getServiceLocator()->get('application')->getMvcEvent();

        $lang = $event->getRouteMatch()->getParam('lang');

        $helper = new FormEditorLight();
        if(isset($config['view_helper_config']['form_editor']['light']) && is_array($config['view_helper_config']['form_editor']['light'])){
              $helper->setOptions($config['view_helper_config']['form_editor']['light']);
        }

        $helper->setOption('theme' ,  'modern');
        $helper->setOption('selector' ,  'div.tinymce_light');

        if (in_array($lang,['de','fr','it','es','hi','ar','ru','zh','tr'])) {
            $helper->setOption('language', $lang);
            $helper->setOption('language_url', $basepath('/js/tinymce-lang/') . $lang .'.js');
        }
        return $helper;
    }
}