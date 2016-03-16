<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** NewApplication.php */
namespace Applications\Mail;

use Applications\Entity\StatusInterface;
use Core\Mail\StringTemplateMessage;
use Applications\Entity\ApplicationInterface;

class StatusChange extends StringTemplateMessage
{
    /**
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * placeholders, which are replaced in the mail
     *
     * @var array
     */
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
     * Sets the application
     *
     * @param ApplicationInterface $application
     * @param string $status
     * @return $this
     */
    public function setApplication(ApplicationInterface $application, $status = null)
    {
        $this->application = $application;
        $this->setTo($application->contact->email, $application->contact->displayName);
        $this->setVariablesFromApplication($application);
        return $this;
    }

    /**
     * Gets the formal salutation of the applicant
     *
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
     * Gets the informal salutation of the applicant
     *
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
     * Gets the title of the job posting
     *
     * @return mixed
     */
    protected function getJobTitle()
    {
        return $this->application->job->title;
    }

    /**
     * Gets the creation date of the application
     *
     * @return string
     */
    protected function getDate()
    {
        /** @var $date \DateTime */
        $date = $this->application->dateCreated;
        return strftime('%x', $date->getTimestamp());
    }
}
