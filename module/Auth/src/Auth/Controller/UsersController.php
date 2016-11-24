<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth controller */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Auth\Repository\User as UserRepository;
use Core\Form\SummaryFormInterface;

/**
 * List registered users
 *
 * @method \Core\Controller\Plugin\CreatePaginator pagination()
 */
class UsersController extends AbstractActionController
{

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    /**
     * List users
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function listAction()
    {
        return $this->pagination([
            'paginator' => ['Auth/User', 'as' => 'users'],
            'form' => [
                [ 'Core/TextSearch', [
                        'elements_options' => [
                            'text_placeholder' => /*@translate*/ 'Type name, email address, role, or login name',
                            'button_element' => 'text',
                        ],
                ]],
                'as' => 'form'
            ],
        ]);
    }

    /**
     * Edit user
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        /* @var $user \Auth\Entity\User */
        $user = $this->userRepository->find($this->params('id'), \Doctrine\ODM\MongoDB\LockMode::NONE, null, ['allowDeactivated' => true]);
        
        // check if user is not found
        if (!$user) {
            return $this->notFoundAction();
        }
        
        $params = $this->params();
        $serviceLocator = $this->serviceLocator;
        $forms = $serviceLocator->get('forms');
        /* @var $infoContainer \Auth\Form\UserProfileContainer */
        $infoContainer = $forms->get('Auth/userprofilecontainer');
        $infoContainer->setEntity($user);
        $statusContainer = $forms->get('Auth/UserStatusContainer');
        $statusContainer->setEntity($user);
        
        // set selected user to image strategy
        $imageStrategy = $infoContainer->getForm('info.image')
            ->getHydrator()
            ->getStrategy('image');
		$fileEntity = $imageStrategy->getFileEntity();
		$fileEntity->setUser($user);
		$imageStrategy->setFileEntity($fileEntity);
        
        if ($this->request->isPost()) {
            $formName = $params->fromQuery('form');
            $container = $formName === 'status' ? $statusContainer : $infoContainer;
            $form = $container->getForm($formName);
        
            if ($form) {
                $postData  = $form->getOption('use_post_array') ? $params->fromPost() : [];
                $filesData = $form->getOption('use_files_array') ? $params->fromFiles() : [];
                $form->setData(array_merge($postData, $filesData));
        
                if (!$form->isValid()) {
                    return new JsonModel(
                        array(
                            'valid' => false,
                            'errors' => $form->getMessages(),
                        )
                    );
                }
                
                $serviceLocator->get('repositories')->store($user);
        
                if ('file-uri' === $params->fromPost('return')) {
                    $content = $form->getHydrator()->getLastUploadedFile()->getUri();
                } else {
                    if ($form instanceof SummaryFormInterface) {
                        $form->setRenderMode(SummaryFormInterface::RENDER_SUMMARY);
                        $viewHelper = 'summaryform';
                    } else {
                        $viewHelper = 'form';
                    }
                    $content = $serviceLocator->get('ViewHelperManager')->get($viewHelper)->__invoke($form);
                }
        
                return new JsonModel(
                    array(
                        'valid' => $form->isValid(),
                        'content' => $content,
                    )
                );
            }
        }
        
        return [
            'infoContainer' => $infoContainer,
            'statusContainer' => $statusContainer
        ];
    }
}
