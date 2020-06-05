<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** NewApplication.php */
namespace Applications\Mail;

use Core\Mail\StringTemplateMessage;
use Applications\Entity\ApplicationInterface;

class AcceptApplication extends StatusChange implements StatusChangeInterface
{

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
        $this->setTo(
            $application->getJob()->getUser()->getInfo()->getEmail(),
            $application->getJob()->getUser()->getInfo()->getDisplayName(false)
        );
        $this->setVariablesFromApplication($application);
        return $this;
    }
}
