<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Core */
namespace Cv\Controller;

use Geo\Form\GeoText;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Core\Form\SummaryFormInterface;
use Auth\Entity\User;
use Cv\Entity\Cv;
use Cv\Entity\Contact;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.
 *
 */
class ManageController extends AbstractActionController
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
    
    public function formAction()
    {
        $serviceLocator = $this->serviceLocator;
        $repositories = $serviceLocator->get('repositories');
        /* @var $cvRepository \Cv\Repository\Cv */
        $cvRepository = $repositories->get('Cv/Cv');
        $user = $this->auth()->getUser();
        /* @var $cv Cv */
        $cv = $cvRepository->findDraft($user);
        $params = $this->params();
        
        if (empty($cv)) {
            // create draft CV
            $cv = $cvRepository->create();
            $cv->setIsDraft(true);
            $cv->setContact($user->getRole() == User::ROLE_USER ? $user->getInfo() : new Contact());
            $cv->setUser($user);
            $repositories->store($cv);
        }
        
        if (($status = $params->fromQuery('status')) != '') {
            return $this->changeStatus($cv, $status);
        }
        
        /* @var $container \Core\Form\Container */
        $container = $serviceLocator->get('FormElementManager')
            ->get('CvContainer')
            ->setEntity($cv);

        // process post method
        if ($this->getRequest()->isPost()) {
            $form = $container->getForm($params->fromQuery('form'));

            if ($form) {
                $form->setData(array_merge(
                    $params->fromPost(),
                    $params->fromFiles()
                ));
                
                if (!$form->isValid()) {
                    return new JsonModel([
                        'valid' => false,
                        'errors' => $form->getMessages()
                    ]);
                }
                /*
                 * @todo This is a workaround for GeoJSON data insertion
                 * until we figured out, what we really want it to be.
                 */
                $formId = $params->fromQuery('form');
                if ('preferredJob' == $formId) {
                    $locElem = $form->getBaseFieldset()->get('geo-location');
                    if ($locElem instanceof GeoText) {
                        $loc = $locElem->getValue('entity');
                        $locations = $cv->getPreferredJob()->getDesiredLocations();
                        if (count($locations)) {
                            $locations->clear();
                        }
                        $locations->add($loc);
                        $cv->getPreferredJob()->setDesiredLocation($locElem->getValue());
                    }
                }

                $repositories->store($cv);
                
                if ($form instanceof SummaryFormInterface) {
                    $form->setRenderMode(SummaryFormInterface::RENDER_SUMMARY);
                    $viewHelper = 'summaryform';
                } else {
                    $viewHelper = 'form';
                }
                
                // render form
                $content = $serviceLocator->get('ViewHelperManager')
                    ->get($viewHelper)
                    ->__invoke($form);
                
                return new JsonModel([
                    'valid' => true,
                    'content' => $content
                ]);
            } elseif (($action = $params->fromQuery('action')) !== null) {
                return new JsonModel($container->executeAction($action, $params->fromPost()));
            }
        }// end of process post method
        else {
            $locElem = $container->getForm('preferredJob')->getBaseFieldset()->get('geo-location');
            if ($locElem instanceof GeoText) {
                $loc = $cv->getPreferredJob()->getDesiredLocations();
                if (count($loc)) {
                    $locElem->setValue($loc->first());
                }
            }
        }

        return [
            'container' => $container,
            'cv' => $cv
        ];
    }

    /**
     *
     * @param Cv $cv
     * @param string $status
     * @return \Zend\Http\Response
     */
    protected function changeStatus(Cv $cv, $status)
    {
        if ($status != $cv->getStatus()) {
            try {
                $cv->setStatus($status);
                
                $this->notification()->success(
                    /*@translate*/ 'Status has been successfully changed');
            } catch (\DomainException $e) {
                $this->notification()->error(
                    /*@translate*/ 'Invalid status');
            }
        }
        
        return $this->redirect()->refresh();
    }
}
