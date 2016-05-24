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
        $serviceLocator  = $this->getServiceLocator();
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
        $serviceLocator = $this->getServiceLocator();
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
        
        $container = $serviceLocator->get('FormElementManager')
            ->get('CvContainer')
            ->setEntity($cv);
        
        // check if CV is empty
        if ($cv->getEmployments()->isEmpty()
            && $cv->getEducations()->isEmpty()
            && $cv->getSkills()->isEmpty())
        {
            // set display mode for CV form
            $container->getForm('cvForm')
                ->setDisplayMode(SummaryFormInterface::RENDER_FORM);
        }
        
        if ($this->getRequest()->isPost()) {
            $params = $this->params();
            $form = $container->getForm($params->fromQuery('form'));
            
            if ($form) {
                // allow empty collections (in addition package zend-form has to be updated minimally to 2.7.*)
                $data = array_merge_recursive([
                        'cv' => [
                            'employments' => [],
                            'educations' => [],
                            'skills' => []
                        ]
                    ],
                    $form->getOption('use_post_array') ? $params->fromPost() : [],
                    $form->getOption('use_files_array') ? $params->fromFiles() : []
                );
                
                $form->setData($data);
                
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
