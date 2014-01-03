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
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Parameters;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.  
 *
 */
class ManageController extends AbstractActionController {

    public function saveAction() {
        if (False) {
            // Test
            $this->request->setMethod('post');
            $params = new Parameters(array(
                'applyId' => 5678, 'company' => '5678_company', 'title' => '5678_title',
                'link' => '5678_link', 'location' => '5678_location',
                'datePublishStart' => '2013-11-11', 'contactEmail' => '5678_contactEmail@web.de',
                 'status' => 'active', 'reference' => 'test_reference',
            ));
            $this->getRequest()->setPost($params);
        }
        //$entity = $services->get('builders')->get('job')->getEntity();
        
        $services = $this->getServiceLocator();
        $user = $services->get('AuthenticationService')->getUser();
        $result = array('token' => session_id(), 'isSaved' => False);
        if (isset($user)) {
            $form = $services->get('FormElementManager')->get('JobForm');
            // determine Job from Database 
            $id = $this->params()->fromPost('id');
            if (empty($id)) {
                $applyId = $this->params()->fromPost('applyId');
                if (empty($applyId)) {
                    // new Job
                    $entity = $services->get('builders')->get('job')->getEntity();
                } else {
                    $entity = $services->get('repositories')->get('job')->findByApplyId((string) $applyId);
                }
            } else {
                $entity = $services->get('repositories')->get('job')->find($id);
            }
            $form->bind($entity);
            if ($this->request->isPost()) {
                $form->setData($this->getRequest()->getPost());
                $result['post'] = $_POST;
                if ($form->isValid()) {
                    $entity->setUserId($user->id);
                    $services->get('repositories')->get('job')->save($entity);
                    $result['isSaved'] = true;
                } else {
                    $result['valid Error'] = $form->getMessages();
                }
            }
        } else {
            $result['message'] = 'session_id is lost';
        }
        return new JsonModel($result);
    }

}

