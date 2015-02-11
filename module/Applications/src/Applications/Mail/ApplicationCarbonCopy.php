<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ApplicationCarbonCopy.php */
namespace Applications\Mail;

/**
 * Sends a Carbon Copy of the submitted application to the applicant
 *
 * @package Applications\Mail
 */
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