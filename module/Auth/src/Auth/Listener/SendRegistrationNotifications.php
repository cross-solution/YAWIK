<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Auth\Listener;

use Auth\Listener\Events\AuthEvent;
use Auth\Options\ModuleOptions;
use Core\Mail\MailService;

/**
 * Listener to send registration notifications.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 * @since 0.30
 */
class SendRegistrationNotifications 
{

    /**
     *
     *
     * @var \Core\Mail\MailService
     */
    private $mailer;

    /**
     *
     *
     * @var \Auth\Options\ModuleOptions
     */
    private $options;

    public function __construct(MailService $mailer, ModuleOptions $options)
    {
        $this->mailer = $mailer;
        $this->options = $options;
    }

    public function __invoke(AuthEvent $e)
    {
        if (!$this->options->notifyOnRegistration()) {
            return;
        }
        
        /* @var \Core\Mail\HTMLTemplateMessage $mail */
        $tmpl = sprintf(
            'auth/mail/%s',
            $e->getName() == AuthEvent::EVENT_USER_REGISTERED
                ? 'new-registration'
                : 'user-confirmed'
        );

        $mail = $this->mailer->get('htmltemplate');
        $mail->setTemplate($tmpl);
        $mail->setVariable('user', $e->getUser());
        $mail->setTo($this->options->getNotificationEmail());
        $mail->renderBodyText(true, $this->options->getNotificationLanguage());

        try {
            $this->mailer->send($mail);
        } catch (\Exception $e) {
            /* Silently ignore all exceptions.
             * @todo Logging
             */
        }
    }
}