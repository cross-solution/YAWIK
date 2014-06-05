<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** NewApplication.php */ 
namespace Applications\Mail;

use Core\Mail\StringTemplateMessage;
use Applications\Entity\ApplicationInterface;

class Confirmation extends StringTemplateMessage
{
    protected $application;
   
    protected $callbacks = array(
        'anrede_formell' => 'getFormalSalutation',
        'salutation_formal' => 'getFormalSalutation',
        'anrede_informell' => 'getInformalSalutation',
        'salutation_informal' => 'getInformalSalutation',
        'job_title' => 'getJobTitle',
        'date' => 'getDate'
    );

    
    public function setVariablesFromApplication(ApplicationInterface $application)
    {
        $contact = $application->contact;
        $name    = $contact->displayName;
        
        $variables = array(
            'name' => $name,
        );
        return $this->setVariables($variables);
    }
    
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
        $this->setTo($application->contact->email, $application->contact->displayName);
        $this->setVariablesFromApplication($application);
        return $this;
    }
    
    public function setRecruiter($recruiter)
    {
        $this->recruiter = $recruiter;
        return $this;
    }
    
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
    
    protected function getInformalSalutation()
    {
        $contact = $this->application->contact;
        $name    = $contact->displayName;
        
        $salutation = $this->getTranslator()
                    ->translate('Hello %s');
        
        return sprintf($salutation, $name);
    }
    
    protected function getJobTitle()
    {
        return $this->application->job->title;
    }
    
    protected function getDate()
    {
        $date = $this->application->dateCreated;
        return strftime('%x', $date->getTimestamp());
    }
    
    public function setSubject($subject, $translate = true)
    {
        $subject = $this->isTranslatorEnabled() && $translate
                 ? $this->getTranslator()->translate($subject, $this->getTranslatorTextDomain())
                 : $subject;
        return parent::setSubject($subject);
    }

}

