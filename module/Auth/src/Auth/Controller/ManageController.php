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
        $info = $user->info instanceOf RelationEntity
              ? $user->info->getEntity()
              : $user->info;
        
        $form->bind($info);
             
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
            
            
                        
                $user->setInfo($info);
                $data = $form->getInputFilter()->getValues();
                $fileData = $data['user-info']['image'];
                
                if ($fileData['error'] == UPLOAD_ERR_OK) {
                    $fileData['field'] = 'image';
                    $imageId = $services->get('repositories')->get('user-file')->saveUploadedFile($fileData);
                    $user->info->setImageId($imageId);
                }
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
    
}

 
