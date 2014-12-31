<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Controller;

use Auth\Form;
use Auth\Service;
use Auth\Service\Exception;
use Core\Controller\AbstractCoreController;
use Zend\Log\LoggerInterface;

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

    public function __construct(Form\Register $form, Service\Register $service, LoggerInterface $logger)
    {
        $this->form = $form;
        $this->service = $service;
        $this->logger = $logger;
    }

    public function indexAction()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        try {
            if ($request->isPost()) {
                $this->form->setData($request->getPost()->toArray() ?: array());
                if ($this->form->isValid()) {
                    $mailer = $this->getPluginManager()->get('Mailer');
                    $url = $this->plugin('url');

                    // we cannot check reCaptcha twice (security protection)
                    $filter = $this->form->getInputFilter()->remove('captcha');
                    $this->service->proceed($filter, $mailer, $url);

                    $this->notification()->success(
                        /*@translate*/ 'An Email with an activation link has been sent, please try to check your email box'
                    );
                } else {
                    $this->notification()->danger(
                        /*@translate*/ 'Please fill form correctly'
                    );
                }
            }
        } catch (Exception\UserAlreadyExistsException $e) {
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

        return array(
            'form' => $this->form
        );
    }
}