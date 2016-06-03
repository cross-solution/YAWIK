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

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Core\Form\SummaryFormInterface;

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

    /**
     * Home site
     *
     */
    public function indexAction()
    {
    }
    
    public function formAction()
    {
        $serviceLocator = $this->serviceLocator;
        $repositories = $serviceLocator->get('repositories');
        /* @var $cvRepository \Cv\Repository\Cv */
        $cvRepository = $repositories->get('Cv/Cv');
        $user = $this->auth()->getUser();
        /* @var $cv \Cv\Entity\Cv */
        $cv = $cvRepository->findDraft($user);
        
        if (empty($cv)) {
            // create draft CV
            $cv = $cvRepository->create();
            $cv->setIsDraft(true);
            $cv->setContact($user->getInfo());
            $cv->setUser($user);
            $repositories->store($cv);
        }
        
        /* @var $container \Core\Form\Container */
        $container = $serviceLocator->get('FormElementManager')
            ->get('CvContainer')
            ->setEntity($cv);
        
        if ($this->getRequest()->isPost()) {
            $params = $this->params();
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
            }
        }
        
        return [
            'container' => $container
        ];
    }
}
