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
        $aaa = $ServiceLocator->get('aaa');
        
        // Holen der der Entity
        $settings = $this->settings();
        // Holen des Formulars
        $form = $this->getServiceLocator()->get('FormElementManager')->get('Settings');
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