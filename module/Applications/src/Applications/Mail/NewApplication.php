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

use Jobs\Entity\JobInterface;
use Core\Mail\StringTemplateMessage;

class NewApplication extends StringTemplateMessage
{
    protected $job;
    protected $user;
    protected $admin;
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
        
        $name = $this->user->info->displayName;
        if ('' == trim($name)) {
            $name = $this->user->info->email;
        }
        
        $variables = array(
            'name' => $name,
            'title' => $this->job->title
        );
        
        $this->setTo($this->user->info->email, $name != $this->user->info->email ? $name : null);
        if ($this->admin && $this->admin->getSettings('Applications')->getMailBCC()) {
            $this->addBcc($this->admin->info->email, $this->admin->info->displayName);
        }
        $this->setVariables($variables);
        $subject = /*@translate*/ 'New application for your vacancy "%s"';
        if ($this->isTranslatorEnabled()) {
            $subject = $this->getTranslator()->translate($subject);
        }
        $this->setSubject(sprintf($subject, $this->job->title));
        
        /* @todo settings retrieved from user entity is an array
         *       not an entity.
         */
        $settings = $this->user->getSettings('Applications');
        $body = $settings->getMailAccessText();
        if ('' == $body) {
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
    
    public function setUser($user)
    {
        $this->user=$user;
        return $this;
    }
    
    public function setAdmin($admin)
    {
        $this->admin = $admin;
        return $this;
    }
    
}

