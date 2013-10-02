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
        
        $name = 'Settings';
        $name = 'Auth';
        $formName = 'Settings/' . $name;
        
        // Fetching an distinct Settings
        $settings = $this->settings($name);
        
        // Write-Access is per default only granted to the own module - change that
        $settings->setAccessWrite();

        
        //$settings = $this->settings();
        //$settingsAuth = $this->settings('auth');
        // Holen des Formulars
        // $form = $settings->getFormular();
        
        $form = $this->getServiceLocator()->get('FormElementManager')->get($formName);
        
        //$formAuth = $this->getServiceLocator()->get('FormElementManager')->get('Settings/Auth');
        
        // Entity an das Formular binden
        $form->bind($settings);
        $data = $this->getRequest()->getPost();
        if (0 < count($data)) {
            $form->setData($data);
            //$form->bindValues($data);
            if ($form->isValid()) {
                // success
            }
            else {
                // fail: error-messages are in the form 
            }
        }
        
        // man könnte hier auch einfach nur ein Array zurückgeben
        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'form' => $form
        ));
        return $viewModel;
    }
}