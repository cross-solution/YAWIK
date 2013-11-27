<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** NewApplication.php */ 
namespace Applications\Mail;

use Jobs\Entity\JobInterface;
use Core\Mail\StringTemplateMessage;

class NewApplication extends StringTemplateMessage
{
    protected $job;
    private $callInitOnSetJob = false;
    
    public function __construct(array $options=array())
    {
        parent::__construct($options);
        $this->callInitOnSetJob = true;
    }
    
    public function init()
    {
        if (!$this->job) {
            return;
        }
        $name = $this->job->user->info->displayName;
        if ('' == trim($name)) {
            $name = $this->job->contactEmail;
        }
        
        $variables = array(
            'name' => $name,
            'title' => $this->job->title
        );
        
        $this->setTo($this->job->contactEmail, $name != $this->job->contactEmail ? $name : null);
        $this->setVariables($variables);
        $subject = /*@translate*/ 'New application for your vacancy "%s"';
        if ($this->isTranslatorEnabled()) {
            $subject = $this->getTranslator()->translate($subject);
        }
        $this->setSubject(sprintf($subject, $this->job->title));
        
        /* @todo settings retrieved from user entity is an array
         *       not an entity.
         */
        $settings = $this->job->user->settings['applications'];
        if (isset($settings['mailAccess']) && $settings['mailAccess']
            && isset($settings['mailAccessText']) && '' != trim($settings['mailAccessText'])
        ) {
            $body = $settings['mailAccessText'];
        } else {
            $body = /*@translate*/ "Hello ##name##,\n\nThere is a new application for your vacancy:\n\"##title##\"\n\n";
            if ($this->isTranslatorEnabled()) {
                $body = $this->getTranslator()->translate($body);
            }
        }
        
        $this->setBody($body);
        return $this;
    }
    
    public function setJob(JobInterface $job, $init = true)
    {
        $this->job = $job;
        if ($this->callInitOnSetJob) { 
            $this->init(); 
        }
        return $this;
    }
    
}

