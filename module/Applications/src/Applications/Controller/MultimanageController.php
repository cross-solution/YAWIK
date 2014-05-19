<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Applications controller */
namespace Applications\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Parameters;

/**
 * Handles managing actions on applications
 */
class MultimanageController extends AbstractActionController {
    
    
    /**
     * some Action on a set of applications,
     * as there are invite, decline, postpone, confirm
     */
    public function multimodalAction() {
        return new JsonModel(array(
            'ok' => true,
            'action' => 'multimodal'
        ));
    }
    
    public function rejectAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $viewHelperManager = $this->getServiceLocator()->get('viewHelperManager');
        $actionUrl = $viewHelperManager->get('url')->__invoke('lang/applications/applications-list', array('action' => 'reject2'));
        $elements = $this->params()->fromPost('elements',array());
        $hidden = '';
        
        //$application   = $repository->find($applicationId);
        $settings = $this->settings();
        $mailService = $this->getServiceLocator()->get('Core/MailService');
        $mail = $mailService->get('Applications/StatusChange');
        //$mail->setApplication($application);
        $mailText      = $settings->mailRejectionText ? $settings->mailRejectionText : '';
        //$mail->setBody($mailText);
        //$mailText = $mail->getBodyText();
       
        foreach ($elements as $element) {
             $hidden .= '<input type="hidden" name="elements[]" value="' . $element. '">';
        }
        
        
        return new JsonModel(array(
            'ok' => true,
            'header' => $translator->translate('confirm you want to delete following Applications'),
            'content' => 'content created ' . date('H:i:s') . '<br /><form action="' . $actionUrl . '">'. 
                    $hidden . 
                    '<textarea class=" form-control " id="mail-content" name="mail-content">' . $mailText . '</textarea></form>'
        ));
    }
    
    public function reject2Action() {
        $elements = $this->params()->fromPost('elements',array());
        return new JsonModel(array());
    }
    
}