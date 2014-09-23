<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth controller */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Core\Form\SummaryFormInterface;


/**
 * Main Action Controller for Authentication module.
 *
 */
class ManageController extends AbstractActionController
{

    /**
     * @return array|JsonModel
     */
    public function profileAction()
    {
        $services = $this->getServiceLocator();
        $container= $services->get('forms')->get('Auth/userprofilecontainer');
        $user     = $services->get('AuthenticationService')->getUser();
        
        $container->setEntity($user);
        
        if ($this->request->isPost()) {
            $formName  = $this->params()->fromQuery('form');
            $form      = $container->getForm($formName);
            $postData  = $form->getOption('use_post_array') ? $_POST : array();
            $filesData = $form->getOption('use_files_array') ? $_FILES : array();
            $data      = array_merge($postData, $filesData);
            $form->setData($data);
            
            if (!$form->isValid()) {
                return new JsonModel(array(
                    'valid' => false,
                    'errors' => $form->getMessages(),
                ));
            }
            
            $this->getServiceLocator()->get('repositories')->store($user);
            
            if ('file-uri' === $this->params()->fromPost('return')) {
                $content = $form->getHydrator()->getLastUploadedFile()->getUri();
            } else {
                if ($form instanceOf SummaryFormInterface) {
                    $form->setRenderMode(SummaryFormInterface::RENDER_SUMMARY);
                    $viewHelper = 'summaryform';
                } else {
                    $viewHelper = 'form';
                }
                $content = $this->getServiceLocator()->get('ViewHelperManager')->get($viewHelper)->__invoke($form);
            }
            
            return new JsonModel(array(
                'valid' => $form->isValid(),
                'content' => $content,
            ));
        }
       return array('form' => $container);
    }

    public function passwordAction()
    {
        $services = $this->getServiceLocator();
        $form     = $services->get('forms')->get('user-password');
        $user     = $services->get('AuthenticationService')->getUser();
        $translator = $services->get('translator');
        
        if (!$user) {
            throw new \Auth\Exception\UnauthorizedAccessException('You must be logged in.');
        }
        $form->bind($user);
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $services->get('repositories')->store($user);
                $vars = array(
                        'ok' => true,
                        'status' => 'success',
                        'text' => $translator->translate('Password successfully changed') . '.',
                );
            } else { // form is invalid
                $vars = array(
                        'ok' => false,
                        'status' => 'error',
                        'text' => $translator->translate('Password could not be changed') . '.',
                );
            }
        }
        
        $vars['form']=$form;
        return $vars;
    }
    
}

 
