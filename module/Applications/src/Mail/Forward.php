<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Forward.php */

namespace Applications\Mail;

use Applications\Entity\Application;
use Core\Factory\ContainerAwareInterface;
use Core\Mail\TranslatorAwareMessage;
use Interop\Container\ContainerInterface;
use Zend\Mime;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
* Sends an e-mail containing an applications
*/
class Forward extends TranslatorAwareMessage implements ContainerAwareInterface
{
    /**
     * @var Application
     */
    protected $application;
    
    /**
     * @var bool
     */
    protected $isInitialized = false;
    
    protected $viewManager;

    /**
     * @param $application
     * @return $this
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
        if ($this->isInitialized) {
            $this->generateBody();
        }
        return $this;
    }
    
    public function init()
    {
        $this->isInitialized = true;
        if (!$this->application) {
            return;
        }
        $this->setEncoding('UTF-8');
        $subject = /* @translate */ 'Fwd: Application to "%s" dated %s';
        if ($this->isTranslatorEnabled()) {
            $subject = $this->getTranslator()->translate($subject);
        }
        $this->setSubject(
            sprintf(
                $subject,
                $this->application->getJob()->getTitle(),
                strftime('%x', $this->application->getDateCreated()->getTimestamp())
            )
        );
        $this->generateBody();
    }

    /**
     * Generates the Mail Body
     */
    protected function generateBody()
    {
        $message = new Mime\Message();

        $text = $this->generateHtml();
        $textPart = new Mime\Part($text);
        $textPart->type = 'text/html';
        $textPart->charset = 'UTF-8';
        $textPart->disposition = Mime\Mime::DISPOSITION_INLINE;
        $message->addPart($textPart);

        if (is_object($this->application->getContact()->getImage()) &&
            $this->application->getContact()->getImage()->getId()) {
            /* @var $image \Auth\Entity\UserImage */
            $image = $this->application->getContact()->getImage();
            $part = new Mime\Part($image->getResource());
            $part->setType($image->getType());
            $part->setEncoding(Mime\Mime::ENCODING_BASE64);
            $part->setFileName($image->getName());
            $part->setDisposition(Mime\Mime::DISPOSITION_ATTACHMENT);
            $message->addPart($part);
        }
        
        foreach ($this->application->getAttachments() as $attachment) { /* @var \Applications\Entity\Attachment $attachment*/
            /* @var  \Applications\Entity\Attachment $part */
            $part = new Mime\Part($attachment->getResource());
            $part->setType($attachment->getType());
            $part->encoding = Mime\Mime::ENCODING_BASE64;
            $part->filename = $attachment->getName();
            $part->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
            $message->addPart($part);
        }
        
        $this->setBody($message);
    }

    /**
     * Generates a mail containing an Application.
     *
     * @return mixed
     */
    protected function generateHtml()
    {
        /*
         * "ViewHelperManager" defined by ZF2
         *  see http://framework.zend.com/manual/2.0/en/modules/zend.mvc.services.html#viewmanager
         */
        $viewManager = $this->viewManager;

        return $viewManager->get("partial")->__invoke('applications/mail/forward', array("application"=>$this->application));
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->viewManager = $container->get('ViewHelperManager');
    }
    
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array $options
     *
     * @return Forward
     */
    public static function factory(ContainerInterface $container, $requestedName, array $options=[])
    {
        $ob = new self($options);
        $ob->setContainer($container);
        return $ob;
    }
}
