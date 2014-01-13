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
use Jobs\Entity\Job;

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
                'applyId' => '179161',
                'company' => 'Kraft von Wantoch GmbH Personalberatung',
                'contactEmail' => 'stephanie.roghmans@kraft-von-wantoch.de',
                'title' => 'Fuhrparkleiter/-in',
                'location' => 'Bundesland, Bayern, DE',
                'link' => 'http://anzeigen.jobsintown.de/job/1/79161.html',
                'datePublishStart' => '2013-11-15',
                'status' => 'aktiv',
                'reference' => '2130010128',
                'camEnabled' => '1',
            ));
            $this->getRequest()->setPost($params);
        }
        
        $services = $this->getServiceLocator();
        $p = $this->params()->fromPost();
        $services->get('Log')->info('Jobs/manage/saveJob ' . var_export($p, True));
        $user = $services->get('AuthenticationService')->getUser();
        $result = array('token' => session_id(), 'isSaved' => False);
        if (isset($user)) {
            $form = $services->get('FormElementManager')->get('JobForm');
            // determine Job from Database 
            $id = $this->params()->fromPost('id');
            if (empty($id)) {
                $applyId = $this->params()->fromPost('applyId');
                if (empty($applyId)) {
                    // new Job (propably this branch is never used since all Jobs should have an apply-Id)
                    $entity = $services->get('repositories')->get('Jobs/Job')->create();
                } else {
                    $entity = $services->get('repositories')->get('Jobs/Job')->findOneBy(array("applyId" => (string) $applyId));
                    if (!isset($entity)) {
                        // new Job (the more likely branch)
                        $entity = $services->get('repositories')->get('Jobs/Job')->create(array("applyId" => (string) $applyId));
                    }
                }
            } else {
                $entity = $services->get('repositories')->get('Jobs/Job')->find($id);
            }
            //$services->get('repositories')->get('Jobs/Job')->store($entity);
            
            $form->bind($entity);
            if ($this->request->isPost()) {
                $params = $this->getRequest()->getPost();
                $params->datePublishStart = \Datetime::createFromFormat("Y-m-d",$params->datePublishStart);
                $form->setData($params);
                $result['post'] = $_POST;
                if ($form->isValid()) {
                    $entity->setUser($user);
                    $services->get('repositories')->get('Jobs/Job')->store($entity);
                    $result['isSaved'] = true;
                } else {
                    $result['valid Error'] = $form->getMessages();
                }
            }
        } else {
            $result['message'] = 'session_id is lost';
        }
        $services->get('Log')->info('Jobs/manage/saveJob result:' . PHP_EOL . var_export($p, True));
        return new JsonModel($result);
    }

}

