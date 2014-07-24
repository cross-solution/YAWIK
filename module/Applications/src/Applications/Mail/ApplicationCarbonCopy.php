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

class ApplicationCarbonCopy extends Forward
{
    public function setApplication($application)
    {
        $this->setTo($application->getContact()->getEmail());
        return parent::setApplication($application);
    } 
    
    public function init() {
        parent::init();
       
        if (!$this->application) {
            return;
        }
       
        $subject = $this->getSubject();
        $subject = substr($subject, 4);
        $this->setSubject($subject);
       
    }
}