<?php
/**
 * Cross Applicant Management
 * 
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Settings controller */
namespace Settings\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
//use Applications\Form\Application as ApplicationForm;
//use Applications\Model\Application as ApplicationModel;
//use Applications\Form\ApplicationHydrator;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Model\JsonModel;

/**
 * Main Action Controller for Applications module.
 *
 */
class IndexController extends AbstractActionController
{
    public function indexAction()
    {   
        $services = $this->getServiceLocator();
        $translator = $services->get('translator');
        $moduleName = $this->params('module');
        
        $settings = $this->settings($moduleName);
        $jsonFormat = 'json' == $this->params()->fromQuery('format');
        if (!$this->getRequest()->isPost() && $jsonFormat) {
            return $settings->toArray();
        }
        
        $modules = $services->get('ModuleManager')->getLoadedModules();
        $modulesWithSettings = $this->config("settings", array_keys($modules));
        
        //$config = $ServiceLocator->get();
        
        $MvcEvent = $this->getEvent();
        $nav = $services->get('main_navigation');
        $settingsMenu = $nav->findOneBy('route', 'lang/settings');
        $settingsMenu->setActive(true);
        
        foreach($modulesWithSettings as $key => $param) {
            $page = array(
                'label' => ucfirst($key),
                'order' => '10',
                'resource' => 'route/lang/settings',
                'route' => 'lang/settings',
                'routeMatch' => $MvcEvent->getRouteMatch(),
                'router' => $MvcEvent->getRouter(),
                'action' => 'index',
                'controller' => 'index',
                'params' => array('lang' => 'de', 'module' => $key),
                'active' => $key == $moduleName
            );
            $settingsMenu->addPage($page);
        }
        
        $formManager = $this->getServiceLocator()->get('FormElementManager');
        $formName = $moduleName . '/SettingsForm';
        if (!$formManager->has($formName)) {
            $formName = "Settings/Form";
        }
        
        // Fetching an distinct Settings
        
        
        // Write-Access is per default only granted to the own module - change that
        $settings->enableWriteAccess();

        
        //$settings = $this->settings();
        //$settingsAuth = $this->settings('auth');
        // Fetch the formular
        
        $form = $formManager->get($formName);
        
        // Binding the Entity to the Formular
        $form->bind($settings);
        $data = $this->getRequest()->getPost();
        if (0 < count($data)) {
            $form->setData($data);
            
            if ($valid = $form->isValid()) {
                $this->getServiceLocator()->get('repositories')->store($settings);
                $vars = array(
                   'status' => 'success',
                   'text' => $translator->translate('Changes successfully saved') . '.');
            } else {
                $vars = array(
                   'status' => 'danger',
                   'text' => $translator->translate('Changes could not be saved') . '.');
            }
        }
        
        if ($jsonFormat) {
            return array('status' => 'success',
                         'settings' => $settings->toArray(),
                        'data' => $data,
                        'valid' => $valid,
                        'errors' => $form->getMessages());
        }

        $vars['form']=$form;
        return $vars;
    }
}