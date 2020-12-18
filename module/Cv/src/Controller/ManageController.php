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

use Core\Repository\RepositoryService;
use Cv\Entity\CvInterface;
use Cv\Service\UploadHandler;
use Geo\Form\GeoSelect;
use Geo\Form\GeoText;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager\FormElementManagerV3Polyfill;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\HelperPluginManager;
use Laminas\View\Model\JsonModel;
use Core\Form\SummaryFormInterface;
use Auth\Entity\User;
use Cv\Entity\Cv;
use Cv\Entity\Contact;
use PHPUnit\Util\Json;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.
 *
 */
class ManageController extends AbstractActionController
{
    private $repositories;
    
    private $formElements;
    
    private $viewHelper;

    private $uploadHandler;

    public function __construct(
        RepositoryService $repositories,
        FormElementManagerV3Polyfill $formElements,
        HelperPluginManager $viewHelper,
        UploadHandler $uploadHandler
    )
    {
        $this->repositories = $repositories;
        $this->formElements = $formElements;
        $this->viewHelper = $viewHelper;
        $this->uploadHandler = $uploadHandler;
    }

    public static function factory(ContainerInterface $container)
    {
        $repositories = $container->get('repositories');
        $formElements = $container->get('FormElementManager');
        $viewHelper = $container->get('ViewHelperManager');
        $uploadHandler = $container->get(UploadHandler::class);

        return new self($repositories, $formElements, $viewHelper, $uploadHandler);
    }

    public function formAction()
    {
        $repositories = $this->repositories;
        /* @var $cvRepository \Cv\Repository\Cv */
        $cvRepository = $repositories->get('Cv/Cv');
        $user = $this->auth()->getUser();
        /* @var $cv Cv */
        $cv = $this->getCv($cvRepository, $user);
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
            $this->acl('Cv/Status', 'change');
            return $this->changeStatus($cv, $status);
        }
        
        /* @var $container \Core\Form\Container */
        $container = $this->formElements
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
                $formId = $params->fromQuery('form');
                $viewHelperManager = $this->viewHelper;
                $uploadHandler = $this->uploadHandler;

                if('image' === $formId){
                    // handles image upload
                    $uploadHandler->handleImageUpload($cv, $_FILES['image']);
                }
                elseif('attachments' === $formId){
                    // handles attachment upload
                    $attachment = $uploadHandler->handleAttachmentUpload($cv, $_FILES['attachments']);
                    $content = $viewHelperManager->get('basepath')
                        ->__invoke($attachment->getUri());
                    return new JsonModel([
                        'valid' => true,
                        'content' => $content,
                    ]);
                }
                else{
                    if (!$form->isValid()) {
                        return new JsonModel([
                            'valid' => false,
                            'errors' => $form->getMessages()
                        ]);
                    };

                    /*
 * @todo This is a workaround for GeoJSON data insertion
 * until we figured out, what we really want it to be.
 */

//                if ('preferredJob' == $formId) {
//                    $locElem = $form->getBaseFieldset()->get('geo-location');
//                    if ($locElem instanceof GeoText) {
//                        $loc = $locElem->getValue('entity');
//                        $locations = $cv->getPreferredJob()->getDesiredLocations();
//                        if (count($locations)) {
//                            $locations->clear();
//                        }
//                        $locations->add($loc);
//                        $cv->getPreferredJob()->setDesiredLocation($locElem->getValue());
//                    }
//                }
                    $this->validateCv($cv);
                    $repositories->store($cv);
                }

                if ('file-uri' === $params->fromPost('return')) {
                    $content = $viewHelperManager->get('basepath')
                        ->__invoke($form->getHydrator()->getLastUploadedFile()->getUri());
                } else {
                    if ($form instanceof SummaryFormInterface) {
                        $form->setRenderMode(SummaryFormInterface::RENDER_SUMMARY);
                        $viewHelper = 'summaryForm';
                    } else {
                        $viewHelper = 'form';
                    }
                    
                    // render form
                    $content = $viewHelperManager->get($viewHelper)
                        ->__invoke($form);
                }
                
                return new JsonModel([
                    'valid' => true,
                    'content' => $content
                ]);
            } elseif (($action = $params->fromQuery('action')) !== null) {
                return new JsonModel($container->executeAction($action, $params->fromPost()));
            }
        }// end of process post method

        return [
            'container' => $container,
            'cv' => $cv
        ];
    }

    /**
     *
     * @param Cv $cv
     * @param string $status
     * @return \Laminas\Http\Response
     */
    protected function changeStatus(Cv $cv, $status)
    {
        if ($status != $cv->getStatus()) {
            try {
                $cv->setStatus($status);
                
                $this->notification()->success(
                    /*@translate*/ 'Status has been successfully changed'
                );
            } catch (\DomainException $e) {
                $this->notification()->error(
                    /*@translate*/ 'Invalid status'
                );
            }
        }
        
        return $this->redirect()->refresh();
    }

    private function getCv($repository, $user)
    {
        $id =
            $this->params()->fromRoute('id')
            ?: (
                $this->params()->fromQuery('id')
                ?: (
                    $this->params()->fromPost('cv')
                    ?: null
                )
            );

        if ('__my__' == $id) {
            return $repository->findOneBy(['user' => $user->getId(), 'isDraft' => null]);
        }

        return $id ? $repository->find($id) : $repository->findDraft($user);
    }

    private function validateCv(Cv $cv)
    {
        if ($cv->getContact()->getEmail()
            && $cv->getPreferredJob()->getDesiredJob()
            && count($cv->getPreferredJob()->getDesiredLocations())
        ) {
            $cv->setIsDraft(false);
        }
    }
}
