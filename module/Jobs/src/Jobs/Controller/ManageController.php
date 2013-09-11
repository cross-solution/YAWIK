<?php

/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** ActionController of Core */
namespace Jobs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.  
 *
 */
class ManageController extends AbstractActionController
{
    public function postAction()
    {
        $services = $this->getServiceLocator();
        $form = $services->get('FormElementManager')->get('JobForm');
        $result = array('token' => session_id());
        
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id');
            if (empty($id)) {
                $entity = $services->get('builders')->get('job')->getEntity();
            } else {
                $entity = $services->get('mappers')->get('job')->find($id);
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
            
            
        }
        
        return new JsonModel($result);
        
        
    }
}
    