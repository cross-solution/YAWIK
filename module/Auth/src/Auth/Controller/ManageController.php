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
            $form->setData($this->request->getPost());
            $form->isValid();
            $user->setInfo($info);
            $services->get('repositories')->get('user')->save($user);
            $vars = array(
                    'ok' => true,
                    'status' => 'success',
                    'text' => $translator->translate('Changes successfully saved') . '.',
                );
            if ($this->request->isXmlHttpRequest()) {
                return new JsonModel($vars);
            }
            $vars['form'] = $form;
            return $vars;
            
        }
        
        return array(
            'form' => $form
        );
         
    }
    
}

 
