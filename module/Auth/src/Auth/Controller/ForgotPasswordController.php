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
use Auth\Service\Exception;
use Core\Controller\AbstractCoreController;
use Zend\Log\LoggerInterface;

class ForgotPasswordController extends AbstractCoreController
{
    /**
     * @var Form\ForgotPassword
     */
    private $form;

    /**
     * @var Service\ForgotPassword
     */
    private $service;
	
	/**
	 * @var LoggerInterface
	 */
    private $logger;

    /**
     * @param Form\ForgotPassword    $form
     * @param Service\ForgotPassword $service
     * @param LoggerInterface        $logger
     */
    public function __construct(
        Form\ForgotPassword $form,
        Service\ForgotPassword $service,
        LoggerInterface $logger
    ) {
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

                    $this->service->proceed($this->form->getInputFilter(), $mailer, $url);

                    $this->notification()->success(
                        /*@translate*/ 'Mail with link for reset password has been sent, please try to check your email box'
                    );
                } else {
                    $this->notification()->danger(
                        /*@translate*/ 'Please fill form correctly'
                    );
                }
            }
        } catch (Exception\UserNotFoundException $e) {
            $this->notification()->danger(
                /*@translate*/ 'User cannot be found for specified username or email'
            );
        } catch (Exception\UserDoesNotHaveAnEmailException $e) {
            $this->notification()->danger(
                /*@translate*/ 'Found user does not have an email'
            );
        } catch (\Auth\Exception\UserDeactivatedException $e) {
            $this->notification()->danger(
                /*@translate*/ 'Found user is inactive'
            );
        } catch (\Exception $e) {
            $this->logger->crit($e);
            $this->notification()->danger(
                /*@translate*/ 'An unexpected error has occurred, please contact your system administrator'
            );
        }

        return array(
            'form' => $this->form
        );
    }
}
