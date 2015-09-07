<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Confirmation.php */
namespace Applications\Mail;

use Core\Mail\StringTemplateMessage;
use Applications\Entity\ApplicationInterface;

/**
 * Sends an confirmation Mail to the applicant.
 *
 * @package Applications\Mail
 */
class Confirmation extends StringTemplateMessage
{
    /**
     * @var ApplicationInterface
     */
    protected $application;
   
    protected $callbacks = array(
        'anrede_formell' => 'getFormalSalutation',
        'salutation_formal' => 'getFormalSalutation',
        'anrede_informell' => 'getInformalSalutation',
        'salutation_informal' => 'getInformalSalutation',
        'job_title' => 'getJobTitle',
        'date' => 'getDate'
    );

    /**
     * @param ApplicationInterface $application
     * @return StringTemplateMessage
     */
    public function setVariablesFromApplication(ApplicationInterface $application)
    {
        $contact = $application->contact;
        $name    = $contact->displayName;
        
        $variables = array(
            'name' => $name,
        );
        return $this->setVariables($variables);
    }

    /**
     * @param ApplicationInterface $application
     * @return $this
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
        $this->setTo($application->contact->email, $application->contact->displayName);
        $this->setVariablesFromApplication($application);
        return $this;
    }

    /**
     * @param $recruiter
     * @return $this
     */
    public function setRecruiter($recruiter)
    {
        $this->recruiter = $recruiter;
        return $this;
    }

    /**
     * @return string
     */
    protected function getFormalSalutation()
    {
        $contact = $this->application->contact;
        $name    = $contact->displayName;
        $gender  = $contact->gender;
        $translator = $this->getTranslator();
        
        $salutation = 'male' == $gender
                    ? $translator->translate('Dear Mr. %s')
                    : $translator->translate('Dear Ms. %s');
        
        return sprintf($salutation, $name);
    }

    /**
     * @return string
     */
    protected function getInformalSalutation()
    {
        $contact = $this->application->contact;
        $name    = $contact->displayName;
        
        $salutation = $this->getTranslator()
                    ->translate('Hello %s');
        
        return sprintf($salutation, $name);
    }

    /**
     * @return mixed
     */
    protected function getJobTitle()
    {
        return $this->application->job->title;
    }

    /**
     * @return string
     */
    protected function getDate()
    {
        /** @var $date \DateTime */
        $date = $this->application->dateCreated;
        return strftime('%x', $date->getTimestamp());
    }

    /**
     * @param string $subject
     * @param bool $translate
     * @return \Zend\Mail\Message
     */
    public function setSubject($subject, $translate = true)
    {
        $subject = $this->isTranslatorEnabled() && $translate
                 ? $this->getTranslator()->translate($subject, $this->getTranslatorTextDomain())
                 : $subject;
        return parent::setSubject($subject);
    }
}
