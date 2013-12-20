<?php
/**
 * Cross Applicant Management
 * 
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth controller */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Core\Entity\RelationEntity;

/**
 * Main Action Controller for Authentication module.
 *
 */
class ManageController extends AbstractActionController
{

    public function myProfileAction()
    {
        $services = $this->getServiceLocator();
        $form     = $services->get('forms')->get('user-profile');
        $user     = $services->get('AuthenticationService')->getUser();
        $translator = $services->get('translator');
        
        if (!$user) {
            throw new \Auth\Exception\UnauthorizedAccessException('You must be logged in.');
            //throw new \Exception('Test');
        }
        
        $form->bind($user);
             
        if ($this->request->isPost()) {
            $files = $this->request->getFiles()->toArray();
            if (!empty($files)) {
                $post = $this->request->getPost()->toArray();
                $data = array_merge_recursive($post, $files);
            } else {
                $data = $this->request->getPost();
            }
            $form->setData($data);
            if ($form->isValid()) {
                $services->get('repositories')->get('user')->save($user);
                $vars = array(
                        'ok' => true,
                        'status' => 'success',
                        'text' => $translator->translate('Changes successfully saved') . '.',
                    );
                if ($this->request->isXmlHttpRequest()) {
                    return new JsonModel($vars);
                }
            } else { // form is invalid
                
                $vars = array(
                        'ok' => false,
                        'status' => 'error',
                        'text' => $translator->translate('Saving changes failed. Please check the marked fields.')
                );
            }
                $vars['form'] = $form;
                return $vars;
            
        }
        
        return array(
            'form' => $form
        );
         
    }
     public function saveApplicationConfirmationAction()
    {
        $services = $this->getServiceLocator();
        $user = $services->get('AuthenticationService')->getUser();
        $result = array('token' => session_id(), 'isSaved' => False);
        if (isset($user)) {
            if ($this->request->isPost()) {
                $body = $this->params()->fromPost('body');
                //$datePublishStart = \DateTime::createFromFormat('Y-m-d',$this->params()->fromPost('datePublishStart'));
                $settings = $this->settings();
                $settings->mailText = $body;
                $result['post'] = $_POST;
            }
        } else {
            $result['message'] = 'session_id is lost';
        }
        return new JsonModel($result);
    }
}

 
