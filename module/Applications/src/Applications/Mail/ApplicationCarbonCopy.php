<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** NewApplication.php */ 
namespace Applications\Mail;

use Core\Mail\StringTemplateMessage;
use Applications\Entity\ApplicationInterface;
use Jobs\Entity\JobInterface;

class ApplicationCarbonCopy extends Forward
{
   public function init() {
       parent::init();
       if (!$this->application) {
            return;
       }
       $subject = /* @translate */ 'Application to "%s" dated %s';
       if ($this->isTranslatorEnabled()) {
            $subject = $this->getTranslator()->translate($subject);
       } 
        $this->setSubject(sprintf(
            $subject,
            $this->application->job->title,
            strftime('%x', $this->application->dateCreated->getTimestamp())
        ));
    }
}