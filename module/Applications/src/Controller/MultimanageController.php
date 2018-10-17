<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Applications controller */
namespace Applications\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Applications\Entity\StatusInterface as Status;

/**
 * Handles multiple actions on applications
 */
class MultimanageController extends AbstractActionController
{

    /**
     * attaches further Listeners for generating / processing the output
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $serviceLocator  = $this->serviceLocator;
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events          = $this->getEventManager();
        $events->attach($defaultServices);
        return $this;
    }

    /**
     * some Action on a set of applications,
     * as there are invite, decline, postpone, confirm
     *
     * @return \Zend\View\Model\JsonModel
     */
    public function multimodalAction()
    {
        return new JsonModel(
            array(
            'ok' => true,
            'action' => 'multimodal'
            )
        );
    }

    /**
     *
     * @TODO consolidate with Manage::status - a lot of shared code
     * @return \Zend\View\Model\JsonModel
     */
    public function rejectApplicationAction()
    {
        $translator        = $this->serviceLocator->get('translator');
        $viewHelperManager = $this->serviceLocator->get('viewHelperManager');
        $actionUrl         = $viewHelperManager->get('url')
                            ->__invoke('lang/applications/applications-list', array('action' => 'rejectApproval'));
        $repository        = $this->serviceLocator->get('repositories')->get('Applications/Application');
        $settings          = $this->settings();
        $mailService       = $this->serviceLocator->get('Core/MailService');

        // re-inject the Application-ids to the formular
        $elements = $this->params()->fromPost('elements', array());
        $hidden = '';
        $displayNames = array();
        foreach ($elements as $element) {
            $hidden .= '<input type="hidden" name="elements[]" value="' . $element . '">';
            $application = $repository->find($element);
            $isAllowed = $this->acl()->test($application, 'change');
            if ($isAllowed) {
                $contact = $application->contact;
                $displayNames[] = $contact->displayName;
            }
        }

        $mailService->get('Applications/StatusChange');

        $mailText = $settings->mailRejectionText ? $settings->mailRejectionText : '';
        $mailSubject = $translator->translate('Your application dated %s');

        // @TODO transfer into form class
        return new JsonModel(
            array(
            'ok' => true,
            'header' => $translator->translate('reject the applicants'),
            'content' => '<form action="' . $actionUrl . '">' .
            $hidden .
            '<input class=" form-control " name="mail-subject" value="'
                . $mailSubject . '"><br /><br />' .
            '<textarea class=" form-control " id="mail-content" name="mail-content">'
                . $mailText . '</textarea></form>'
            )
        );
    }

    /**
     *
     * @return \Zend\View\Model\JsonModel
     */
    public function rejectApprovalAction()
    {
        $translator        = $this->serviceLocator->get('translator');
        $repositoryService = $this->serviceLocator->get('repositories');
        $repository        = $repositoryService->get('Applications/Application');
        $mailService       = $this->serviceLocator->get('Core/MailService');
        $elements          = $this->params()->fromPost('elements', array());
        foreach ($elements as $element) {
            $mail = $mailService->get('Applications/StatusChange');
            /* @var \Applications\Entity\Application $application */
            $application = $repository->find($element);
            $mail->setApplication($application);
            $mail->setBody($this->params()->fromPost('mail-content'));
            $mailSubject = sprintf(
                $translator->translate($this->params()->fromPost('mail-subject')),
                strftime('%x', $application->dateCreated->getTimestamp())
            );
            $mail->setSubject($mailSubject);

            if ($from = $application->job->contactEmail) {
                $mail->setFrom($from, $application->job->company);
            }
            if ($this->settings()->mailBCC) {
                $user = $this->auth()->getUser();
                $mail->addBcc($user->info->email, $user->info->displayName);
            }
            $mailService->send($mail);

            // update the Application-History
            $application->changeStatus(
                Status::REJECTED,
                sprintf(
                           /*@translate */ 'Mail was sent to %s',
                                           $application->contact->email
                )
            );
            $repositoryService->store($application);
            unset($mail);
        }
        return new JsonModel(array('ok' => true, ));
    }
    
    /**
     * Move given applications to Talent Pool
     *
     * @since 0.26
     */
    public function moveAction()
    {
        $ids = (array)$this->params()->fromPost('ids');
        $moved = 0;
        
        if ($ids) {
            $serviceManager = $this->serviceLocator;
            $repositories = $serviceManager->get('repositories');
            $applicationRepository = $repositories->get('Applications/Application');
            $cvRepository = $repositories->get('Cv/Cv');
            $user = $this->auth()->getUser();
            
            foreach ($ids as $id) {
                $application = $applicationRepository->find($id);
                
                if (!$application) {
                    continue;
                }
                
                if (!$this->acl($application, 'move', 'test')) {
                    continue;
                }
                
                $cv = $cvRepository->createFromApplication($application, $user);
                $repositories->store($cv);
                $repositories->remove($application);
                $moved++;
            }
        }
        
        $this->notification()->success(
            sprintf(
                /*@translate */ '%d Application(s) has been successfully moved to Talent Pool',
                $moved
        )
        );
        
        return $this->redirect()->toRoute('lang/applications');
    }
}
