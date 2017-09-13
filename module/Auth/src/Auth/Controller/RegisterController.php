<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Controller;

use Auth\Form;
use Auth\Service;
use Auth\Options\ModuleOptions;
use Auth\Service\Exception;
use Core\Controller\AbstractCoreController;
use Zend\Log\LoggerInterface;
use Zend\View\Model\ViewModel;

class RegisterController extends AbstractCoreController
{
    /**
     * @var Form\Register
     */
    private $form;

    /**
     * @var Service\Register
     */
    private $service;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ModuleOptions
     */
    private $options;


    public function __construct(
        Form\RegisterFormInterface $form,
        Service\Register $service,
        LoggerInterface $logger,
        ModuleOptions $options)
    {
        $this->form = $form;
        $this->service = $service;
        $this->logger = $logger;
        $this->options = $options;
    }

    public function indexAction()
    {
        if (!$this->options->getEnableRegistration()) {
            $this->notification()->info(/*@translate*/ 'Registration is disabled');
            return $this->redirect()->toRoute('lang');
        }

        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        $viewModel = new ViewModel();


        try {
            if ($request->isPost()) {
                $this->form->setData($request->getPost()->toArray() ?: array());
                if ($this->form->isValid()) {
                    $mailer = $this->getPluginManager()->get('Mailer');
                    $url = $this->plugin('url');

                    // we cannot check reCaptcha twice (security protection) so we have to remove it
                    $filter = $this->form->getInputFilter()->remove('captcha');
                    $this->service->proceed($filter, $mailer, $url);

                    $this->notification()->success(
                        /*@translate*/ 'An Email with an activation link has been sent, please try to check your email box'
                    );

                    $viewModel->setTemplate('auth/register/completed');
                } else {
                    $viewModel->setTemplate(null);
                    $this->notification()->danger(
                        /*@translate*/ 'Please fill form correctly'
                    );
                }
            } else {
                /* @var $register \Zend\Form\Fieldset */
                $register = $this->form->get('register');
                $register->get('role')->setValue($this->params('role'));
            }
        } catch (Exception\UserAlreadyExistsException $e) {
            $this->notification()->danger(
                /*@translate*/ 'User with this email address already exists'
            );
        } catch (\Auth\Exception\UserDeactivatedException $e) {
            $this->notification()->danger(
                /*@translate*/ 'User with this email address already exists'
            );
        } catch (\Exception $e) {
            $this->logger->crit($e);
            $this->notification()->danger(
                /*@translate*/ 'An unexpected error has occurred, please contact your system administrator'
            );
        }

        $this->form->setAttribute('action', $this->url()->fromRoute('lang/register'));

        $viewModel->setVariable('form', $this->form);

        return $viewModel;
    }
}
