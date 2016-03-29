<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ApplicationCarbonCopy.php */
namespace Applications\Mail;

use Applications\Entity\Application;

/**
 * Sends a Carbon Copy of the submitted application to the applicant
 *
 * @package Applications\Mail
 */
class ApplicationCarbonCopy extends Forward
{
    /**
     * @param $application Application
     * @return $this
     */
    public function setApplication(Application $application)
    {
        /* Applications\Entity\Contact */
        $this->setTo($application->getContact()->getEmail());
        return parent::setApplication($application);
    }

    /**
     * @return bool
     */
    public function init()
    {
        parent::init();
       
        if (!$this->application) {
            return false;
        }
       
        $subject = $this->getSubject();
        $subject = substr($subject, 4);
        $this->setSubject($subject);
        return true;
    }
}
