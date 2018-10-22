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

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Core\Form\View\Helper\FormEditorLight;

/**
 * Class FormEditorLightFactory
 *
 * @package Core\Factory\Form\View\Helper
 */
class FormEditorLightFactory implements FactoryInterface
{


    /**
     * Create a FormEditorLight view helper
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return FormEditorLight
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $basePath = $container->get('ViewHelperManager')->get('basepath');
        $config   = $container->get('Config');
    
        /* @var \Zend\Mvc\MvcEvent $event */
        $event = $container->get('application')->getMvcEvent();
    
        $lang = $event->getRouteMatch()->getParam('lang');
    
        $helper = new FormEditorLight();
        if (isset($config['view_helper_config']['form_editor']['light']) && is_array($config['view_helper_config']['form_editor']['light'])) {
            $helper->setOptions($config['view_helper_config']['form_editor']['light']);
        }
    
        $helper->setOption('theme', 'modern');
        $helper->setOption('selector', 'div.tinymce_light');
    
        if (in_array($lang, [ 'de', 'fr', 'it', 'es', 'hi', 'ar', 'ru', 'zh', 'tr' ])) {
            $helper->setOption('language', $lang);
            $helper->setOption('language_url', $basePath('modules/Core/js/tinymce-lang/') . $lang . '.js');
        }
    
        return $helper;
    }
}
