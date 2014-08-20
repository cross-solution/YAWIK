<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
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

class MailService extends AbstractPluginManager
{
    
    protected $transport;
    protected $from;
    protected $mailer;
    
    protected $overrideRecipient;

    protected $shareByDefault = false;
    
    protected $invokableClasses = array(
        'simple' => '\Zend\Mail\Message',
        'stringtemplate' => '\Core\Mail\StringTemplateMessage'
    );
    
    protected $factories = array(
    
    );
     
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
     * @return the $transport
     */
    public function getTransport ()
    {
        return $this->transport;
    }

	/**
     * @param field_type $transport
     */
    public function setTransport (TransportInterface $transport)
    {
        $this->transport = $transport;
        return $this;
    }

    public function getFrom()
    {
        return $this->from;
    }
    
    public function setFrom($email, $name=null)
    {
        $this->from = is_object($email) || null === $name 
                    ? $email 
                    : array($email => $name);
        
        return $this;
    }
    
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
        return $this;
    }
    
    public function getMailer()
    {
        return $this->mailer;
    }
    
    public function setOverrideRecipient(AddressList $recipients)
    {
        $this->overrideRecipient = $recipients;
        return $this;
    }
    
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

