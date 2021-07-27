<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** Forward.php */

namespace Applications\Mail;

use Applications\Entity\Application;
use Core\Mail\TranslatorAwareMessage;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Laminas\Mime;
use Laminas\View\HelperPluginManager;

/**
* Sends an e-mail containing an applications
*/
class Forward extends TranslatorAwareMessage
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

    protected $fileRepository;


    public function __construct(HelperPluginManager $viewHelperManager, DocumentRepository $attachmentRepository, array $options = [])
    {
        $this->viewManager = $viewHelperManager;
        $this->fileRepository = $attachmentRepository;
        parent::__construct($options);
    }

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

        foreach ($this->application->getAttachments() as $attachment) {
            /** @var \Applications\Entity\Attachment $attachment */
            $stream = $this->fileRepository->openDownloadStream($attachment->getId());
            $part = new Mime\Part($stream);
            $part->setType($attachment->getMetadata()->getContentType());
            $part->encoding = Mime\Mime::ENCODING_BASE64;
            $part->filename = $attachment->getMetadata()->getName();
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
}
