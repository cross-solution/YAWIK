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

use Applications\Entity\StatusInterface;
use Core\Mail\StringTemplateMessage;
use Applications\Entity\ApplicationInterface;

interface StatusChangeInterface
{

    /**
     * @param ApplicationInterface $application
     * @return StringTemplateMessage
     */
    public function setVariablesFromApplication(ApplicationInterface $application);

    /**
     * Sets the application
     *
     * @param ApplicationInterface $application
     * @param string|null $status
     * @return $this
     */
    public function setApplication(ApplicationInterface $application, $status = null);
}
