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

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.  
 *
 */
class ManageController extends AbstractActionController
{
    public function saveAction()
    {
        $services = $this->getServiceLocator();
        $user = $services->get('AuthenticationService')->getUser();
        $result = array('token' => session_id(), 'isSaved' => False);
        if (isset($user)) {
            $form = $services->get('FormElementManager')->get('JobForm');
            $id = 0;
            $applyId = 0;
            $company = 0;
            $title = '';
            $link = '';
            $datePublishStart = '';
            if ($this->request->isPost()) {
                $id = $this->params()->fromPost('id');
                $applyId = $this->params()->fromPost('applyId');
                $company = $this->params()->fromPost('company');
                $title = $this->params()->fromPost('title');
                $link = $this->params()->fromPost('link');
                $datePublishStart = $this->params()->fromPost('datePublishStart');
            }
            if (empty($id)) {
                if (empty($applyId)) {
                    $entity = $services->get('builders')->get('job')->getEntity();
                } else {
                    $entity = $services->get('repositories')->get('job')->findByApplyId((string) $applyId);
                }
            } else {
                $entity = $services->get('repositories')->get('job')->find($id);
            }
            if (isset($entity)) {
                $form->bind($entity);
                $form->setData(
                        array('job' =>
                            array(
                                'applyId' => $applyId,
                                //'source' => 'AMS',
                                'company' => $company,
                                'title' => $title,
                                'link' => $link,
                                'datePublishStart' => $datePublishStart,
                            )
                        )
                );
                if ($form->isValid()) {
                    $entity->setUserId($user->id);
                    $services->get('repositories')->get('job')->save($entity);
                    $result['isSaved'] = true;
                }
            }
        } else {
            $result['message'] = 'session_id is lost';
        }
        return new JsonModel($result);
        
        
    }
}
    