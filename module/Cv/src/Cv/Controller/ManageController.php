<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** ActionController of Core */
namespace Cv\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.  
 *
 */
class ManageController extends AbstractActionController
{
    
    /**
     * Home site
     *
     */
    public function indexAction()
    { }
    
    public function formAction()
    {
        $services = $this->getServiceLocator();
        $form = $services->get('FormElementManager')->get('CvForm');
        
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id');
            if (empty($id)) {
                $entity = $services->get('builders')->get('cv')->getEntity();
            } else {
                $entity = $services->get('mappers')->get('cv')->find($id);
            }
            $form->bind($entity);
            $form->setData($this->request->getPost());
            $valid = $form->isValid();
            if ($valid) {
                $services->get('repositories')->get('cv')->save($entity);
                return array(
                    'isSaved' => true,
                );
            }
            
            exit;    
        }
        
        return array(
            'form' => $form
        );
    }
    
    public function saveAction()
    {
        
    }
    
}
