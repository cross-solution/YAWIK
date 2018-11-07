<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Confirmation.php */
namespace Applications\Mail;

use Auth\Entity\AnonymousUser;
use Auth\Entity\UserInterface;
use Core\Mail\StringTemplateMessage;
use Applications\Entity\ApplicationInterface;
use Zend\Mvc\Router\RouteStackInterface;

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
        'date' => 'getDate',
        'link' => 'getApplicationLink',
    );

    /**
     *
     *
     * @var RouteStackInterface
     */
    protected $router;

    /**
     *
     *
     * @var UserInterface
     */
    protected $user;

    /**
     * @param RouteStackInterface $router
     *
     * @return self
     */
    public function setRouter($router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * @param UserInterface $user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }




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
     * @param ApplicationInterface $application
     * @return $this
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
        $this->setTo($application->getContact()->getEmail(), $application->getContact()->getDisplayName());
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
        $contact = $this->application->getContact();
        $name    = $contact->getDisplayName();
        $gender  = $contact->getGender();
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
        $contact = $this->application->getContact();
        $name    = $contact->getDisplayName();
        
        $salutation = $this->getTranslator()
                    ->translate('Hello %s');
        
        return sprintf($salutation, $name);
    }

    /**
     * @return mixed
     */
    protected function getJobTitle()
    {
        return $this->application->getJob()->getTitle();
    }

    /**
     * @return string
     */
    protected function getDate()
    {
        /** @var $date \DateTime */
        $date = $this->application->getDateCreated();
        return strftime('%x', $date->getTimestamp());
    }

    protected function getApplicationLink()
    {
        $router = $this->router;
        $user   = $this->user;

        if (!$router || !$user) {
            return '';
        }

        $token = $user instanceof AnonymousUser ? '?token=' . $user->getToken() : '';
        $href  = $router->assemble(
                        ['id' => $this->application->getId()],
                        ['name'=>'lang/applications/detail', 'force_canonical'=>true]
        ) . $token;

        return $href;
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
