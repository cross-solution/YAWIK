<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

/** */
namespace Core\Mail;

use Core\Factory\ContainerAwareInterface;
use Interop\Container\ContainerInterface;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\Mail\Address;
use Zend\Mail\AddressList;
use Zend\Mail\Message as MailMessage;
use Zend\Mail\Transport\TransportInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Mail Plugin Manager
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 */
class MailService extends AbstractPluginManager
{
    /**
     * Define transport type to use
     */
    const TRANSPORT_SMTP        = 'smtp';
    const TRANSPORT_FILE        = 'file';
    const TRANSPORT_SENDMAIL    = 'sendmail';

    /**
     * The mail Transport
     *
     * @var TransportInterface
     */
    protected $transport;

    /**
     * Default from address to use if no from address is set in the mail.
     *
     * @var string
     */
    protected $from;

    /**
     * Value for the X-Mailer header.
     *
     * @var string
     */
    protected $mailer;

    /**
     * If set, all mails are send to the addresses in this list
     *
     * This is useful when developing.
     *
     * @var AddressList|null
     */
    protected $overrideRecipient;

    protected $language;

    protected $shareByDefault = false;

    protected $invokableClasses = array(
        'simple'         => '\Zend\Mail\Message',
        'stringtemplate' => '\Core\Mail\StringTemplateMessage',
    );

    protected $factories = array(
        'htmltemplate'   => [HTMLTemplateMessage::class,'factory'],
    );

    /**
     * Creates an instance.
     *
     * Adds two default initializers:
     * - Inject the translator to mails implementing TranslatorAwareInterface
     * - Call init() method on Mails if such method exists.
     *
     * @param ContainerInterface $container
     * @param mixed $configuration
     */
    public function __construct($container, $configuration = [])
    {
        parent::__construct($container, $configuration);
        
        $this->addInitializer(
            function ($context, $instance) {
                if ($instance instanceof TranslatorAwareInterface) {
                    $translator = $context->get('translator');
                    $instance->setTranslator($translator);
                    if (null === $instance->getTranslatorTextDomain()) {
                        $instance->setTranslatorTextDomain();
                    }
                    $instance->setTranslatorEnabled(true);
                }
                if ($instance instanceof ContainerAwareInterface) {
                    $instance->setContainer($context);
                }
            }
        );
        
        //@TODO: [ZF3] verify that removing this lines is save
        //$this->addInitializer(
        //   function ($context,$instance) {
        //        if (method_exists($instance, 'setServiceLocator')) {
        //            //$instance->setServiceLocator($this);
        //        }
        //   }
        //);
        
        $this->addInitializer(
            function ($context, $instance) {
                if (method_exists($instance, 'init')) {
                    $instance->init();
                }
            }
        );
    }

    /**
     * Checks that a plugin is a child of an email message.
     *
     * @throws \InvalidArgumentException
     */
    public function validate($plugin)
    {
        if (!$plugin instanceof MailMessage) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected instance of \Zend\Mail\Message but received %s',
                    get_class($plugin)
                )
            );
        }
    }

    /**
     * Gets the default from address
     *
     * @return null|String|Address|AddressList|array
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Sets the default from address.
     *
     * @param string|AddressList|Address $email
     * @param String|null                $name
     *
     * @return self
     */
    public function setFrom($email, $name = null)
    {
        if (is_array($email)) {
            $this->from = [$email['email'] => $email['name']];
        } else {
            $this->from = is_object($email) || null === $name
                ? $email
                : array($email => $name);
        }

        return $this;
    }

    /**
     * Sets override recipients.
     *
     * @param AddressList $recipients
     *
     * @return self
     */
    public function setOverrideRecipient(AddressList $recipients)
    {
        $this->overrideRecipient = $recipients;

        return $this;
    }

    /**
     * Sends a mail.
     *
     * Sets default values where needed.
     *
     * @param string|MailMessage $mail
     * @param array          $options
     */
    public function send($mail, array $options = array())
    {
        if (!$mail instanceof MailMessage) {
            $mail = $this->get($mail, $options);
        }

        $headers   = $mail->getHeaders();
        $transport = $this->getTransport();

        if (!$mail->isValid() && $this->from) {
            $mail->setFrom($this->from);
        }

        if ($this->overrideRecipient instanceof AddressList) {
            $originalRecipient = $headers->get('to')->toString();
            if ($headers->has('cc')) {
                $originalRecipient .= '; ' . $headers->get('cc')->toString();
                $headers->removeHeader('cc');
            }
            if ($headers->has('bcc')) {
                $originalRecipient .= '; ' . $headers->get('bcc')->toString();
                $headers->removeHeader('bcc');
            }
            $headers->addHeaderLine('X-Original-Recipients', $originalRecipient);
            $mail->setTo($this->overrideRecipient);
        }

        if (!$headers->has('X-Mailer')) {
            $mailerHeader = new \Zend\Mail\Header\GenericHeader('X-Mailer', $this->getMailer());
            $headers->addHeader($mailerHeader);
            $mailerHeader->setEncoding('ASCII'); // get rid of other encodings for this header!
        }

        /* Allow HTMLTemplateMails to alter subject in the view script.
         * As the Zend Transport class build subject before the getBodyText call,
         * we have to call it here.
         */
        if ($mail instanceof \Core\Mail\HTMLTemplateMessage) {
            $mail->getBodyText();
        }

        $transport->send($mail);
    }

    /**
     * Gets the transport.
     *
     * @return TransportInterface $transport
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * Sets the transport
     *
     * @param TransportInterface $transport
     *
     * @return self
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;

        return $this;
    }

    /**
     * Gest the value of the X-Mailer header.
     *
     * @return string
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * Sets the value for the X-Mailer header
     *
     * @param string $mailer
     *
     * @return $this
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;

        return $this;
    }
}
