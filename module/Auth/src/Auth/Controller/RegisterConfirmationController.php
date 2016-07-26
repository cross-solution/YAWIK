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

class RegisterConfirmationController extends AbstractCoreController
{
    /**
     * @var Service\RegisterConfirmation
     */
    private $service;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Service\RegisterConfirmation $service, LoggerInterface $logger)
    {
        $this->service = $service;
        $this->logger = $logger;
    }

    public function indexAction()
    {
        $userId = $this->params()->fromRoute('userId', null);

        try {
            $this->service->proceed($userId);

            $this->notification()->info(
                /*@translate*/ 'User email verified successfully. You need to set a password to log in.'
            );

            return $this->redirect()->toRoute('lang/my', array('action' => 'password'));
        } catch (Exception\UserNotFoundException $e) {
            $this->notification()->danger(
                /*@translate*/ 'User cannot be found'
            );
        } catch (\Exception $e) {
            $this->logger->crit($e);
            $this->notification()->danger(
                /*@translate*/ 'An unexpected error has occurred, please contact your system administrator'
            );
        }

        return $this->redirect()->toRoute('lang/register');
    }
}
