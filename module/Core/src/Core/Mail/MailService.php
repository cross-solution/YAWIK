<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** MailService.php */ 
namespace Core\Mail;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ConfigInterface;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mail\Message;
use Zend\Mail\AddressList;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\I18n\Translator\TranslatorAwareInterface;

/**
 * Class MailService
 * @package Core\Mail
 */
class MailService extends AbstractPluginManager
{
    /**
     * @var TransportInterface
     */
    protected $transport;

    /**
     * @var String From Mail Header
     */
    protected $from;

    protected $mailer;

    /**
     * @var boolean The recipient can be overwritten. This helps developers...
     */
    protected $overrideRecipient;

    /**
     * @var bool
     */
    protected $shareByDefault = false;
    
    protected $invokableClasses = array(
        'simple' => '\Zend\Mail\Message',
        'stringtemplate' => '\Core\Mail\StringTemplateMessage',
        'htmltemplate' => '\Core\Mail\HTMLTemplateMessage'
    );
    
    protected $factories = array(
    
    );

    /**
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        $self = $this;

        $this->addInitializer(function($instance) use ($self) {
            if ($instance instanceOf TranslatorAwareInterface) {
                $translator = $self->getServiceLocator()->get('translator');
                $instance->setTranslator($translator);
                $instance->setTranslatorEnabled(true);
            }
        }, /*topOfStack*/ false);
        $this->addInitializer(function($instance) {
            if (method_exists($instance, 'init')) {
                $instance->init();
            }
        }, false);
        
    }

    /**
     * @param mixed $plugin
     * @throws \InvalidArgumentException
     */
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceOf Message) {
            throw new \InvalidArgumentException(sprintf(
                'Expected instance of \Zend\Mail\Message but received %s',
                get_class($plugin)
            ));
        }
    }

	/**
     * @return TransportInterface $transport
     */
    public function getTransport ()
    {
        return $this->transport;
    }

	/**
     * @param TransportInterface $transport
     */
    public function setTransport (TransportInterface $transport)
    {
        $this->transport = $transport;
        return $this;
    }

    /**
     * @return String
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param $email
     * @param String|null $name
     * @return $this
     */
    public function setFrom($email, $name=null)
    {
        $this->from = is_object($email) || null === $name 
                    ? $email 
                    : array($email => $name);
        
        return $this;
    }

    /**
     * @param $mailer
     * @return $this
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @param AddressList $recipients
     * @return $this
     */
    public function setOverrideRecipient(AddressList $recipients)
    {
        $this->overrideRecipient = $recipients;
        return $this;
    }

    /**
     * @param $mail
     * @param array $options
     */
    public function send($mail, array $options=array())
    {
        if (!$mail instanceOf Message) {
            $mail = $this->get($mail, $options);
        }
        
        $headers   = $mail->getHeaders();
        $transport = $this->getTransport();
        
        if (!$mail->isValid() && $this->from) {
            $mail->setFrom($this->from);
        }
        
        if (null !== $this->overrideRecipient) {
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
        
        $transport->send($mail);
    }
}

