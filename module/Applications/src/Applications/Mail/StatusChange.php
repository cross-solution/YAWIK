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

use Core\Mail\StringTemplateMessage;
use Applications\Entity\ApplicationInterface;

class StatusChange extends StringTemplateMessage implements StatusChangeInterface
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
        $contact = $application->getContact();
        $name    = $contact->getDisplayName();
        
        $variables = array(
            'name' => $name,
        );
        return $this->setVariables($variables);
    }

    /**
     * Sets the application
     *
     * @param ApplicationInterface $application
     * @param string|null $status
     * @return $this
     */
    public function setApplication(ApplicationInterface $application, $status = null)
    {
        $this->application = $application;
        $this->setTo($application->getContact()->getEmail(), $application->getContact()->getDisplayName(false));
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
        $contact = $this->application->getContact();
        $name    = $contact->getLastName();
        $gender  = $contact->getGender();
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
        $contact = $this->application->getContact();
        $name    = $contact->getDisplayName(false);
        
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
        return $this->application->getJob()->getTitle();
    }

    /**
     * Gets the creation date of the application
     *
     * @return string
     */
    protected function getDate()
    {
        /** @var $date \DateTime */
        $date = $this->application->getDateCreated();
        return strftime('%x', $date->getTimestamp());
    }
}
