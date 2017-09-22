<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Listener;

use Auth\AuthenticationService;
use Zend\Mvc\Service\ControllerPluginManagerFactory;
use Auth\Listener\Events\AuthEvent;
use Auth\Options\ModuleOptions;
use Core\Options\ModuleOptions as CoreOptions;
use Core\Mail\MailService;

/**
 * @package Auth\Listener
 */
class MailForgotPassword
{
    /**
     * @var ModuleOptions;
     */
    protected $options;

    /**
     * @var CoreOptions;
     */
    protected $coreOptions;

    /**
     * @var MailService
     */
    protected $mailService;

    public function __construct($options, $mailService, $coreOptions)
    {
        $this->options=$options;
        $this->mailService=$mailService;
        $this->coreOptions = $coreOptions;
    }

    /**
     * @param AuthEvent $event
     *
     * @return mixed
     */
    public function __invoke(AuthEvent $event)
    {
    	/* @TODO: [ZF3] $e->getUser() is not worked anymore we should using $e->getTarget()->getUser() */
    	$target     = $event->getTarget();
        $siteName   = $this->coreOptions->getSiteName();
        /* @var \Auth\Entity\User $user */
        $user                    = $target->getUser();
        $userEmail               = $user->getInfo()->getEmail();
        $userName                = $user->getInfo()->getDisplayName(false);
        $resetLink               = $target->getResetLink();

        $fromEmail               =  $this->options->getFromEmail();
        $fromName                =  $this->options->getFromName();


        $mail                    = $this->mailService->get('htmltemplate');
        $mail->user              = $user;
        $mail->resetlink         = $resetLink;
        $mail->setTemplate('mail/forgotPassword');
        $mail->setSubject(/*@translate*/ 'a new password was requested for %s', $siteName);
        $mail->setTo($userEmail, $userName);
        $mail->setFrom($fromEmail, $fromName);
        return $this->mailService->send($mail);
    }
}
