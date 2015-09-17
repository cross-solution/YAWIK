<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Controller;

use Auth\Form;
use Auth\Service;
use Auth\Service\Exception;
use Core\Controller\AbstractCoreController;
use Zend\Log\LoggerInterface;

class GotoResetPasswordController extends AbstractCoreController
{

    /**
     * @var Service\GotoResetPassword
     */
    private $service;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Service\GotoResetPassword $service
     * @param LoggerInterface           $logger
     */
    public function __construct(
        Service\GotoResetPassword $service,
        LoggerInterface $logger
    ) {
        $this->service = $service;
        $this->logger = $logger;
    }

    public function indexAction()
    {
        $userId = $this->params()->fromRoute('userId', null);
        $token = $this->params()->fromRoute('token', null);

        try {
            $this->service->proceed($userId, $token);

            return $this->redirect()->toRoute('lang/my', array('action' => 'password'));
        } catch (Exception\TokenExpirationDateExpiredException $e) {
            $this->notification()->danger(
                /*@translate*/ 'Cannot proceed, token expired'
            );
        } catch (Exception\UserNotFoundException $e) {
            $this->notification()->danger(
                /*@translate*/ 'User cannot be found for specified token'
            );
        } catch (\Exception $e) {
            $this->logger->crit($e);
            $this->notification()->danger(
                /*@translate*/ 'An unexpected error has occurred, please contact your system administrator'
            );
        }

        return $this->redirect()->toRoute('lang/forgot-password');
    }
}
