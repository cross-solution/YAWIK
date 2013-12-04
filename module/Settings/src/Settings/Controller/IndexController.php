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
        $ServiceLocator = $this->getServiceLocator();
        
        $moduleName = $this->params('module');
        
        $settings = $this->settings($moduleName);
        $jsonFormat = 'json' == $this->params()->fromQuery('format');
        if (!$this->getRequest()->isPost() && $jsonFormat) {
            return $settings->toArray();
        }
        
        $modules = $ServiceLocator->get('ModuleManager')->getLoadedModules();
        $modulesWithSettings = $this->config("settings", array_keys($modules));
        
        //$config = $ServiceLocator->get();
        
        $MvcEvent = $this->getEvent();
        $nav = $ServiceLocator->get('main_navigation');
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
        
        
        $formName = 'Settings/' . $moduleName;
        
        // Fetching an distinct Settings
        
        
        // Write-Access is per default only granted to the own module - change that
        $settings->setAccessWrite();

        
        //$settings = $this->settings();
        //$settingsAuth = $this->settings('auth');
        // Fetch the formular
        
        $form = $this->getServiceLocator()->get('FormElementManager')->get($formName);
        
        // Binding the Entity to the Formular
        $form->bind($settings);
        $data = $this->getRequest()->getPost();
        if (0 < count($data)) {
            $form->setData($data);
            //$form->bindValues($data);
            if ($valid = $form->isValid()) {
                // success
            }
            else {
                // fail: error-messages are in the form 
            }
        }
        
        if ($jsonFormat) {
            return array('status' => 'success',
                         'settings' => $settings->toArray(),
                        'data' => $data,
                        'valid' => $valid,
                        'errors' => $form->getMessages());
        }
        // man könnte hier auch einfach nur ein Array zurückgeben
        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'form' => $form,
        ));
        return $viewModel;
    }
}