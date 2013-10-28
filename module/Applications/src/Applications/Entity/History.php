<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** History.php */ 
namespace Applications\Entity;

use Core\Entity\AbstractEntity;

class History extends AbstractEntity implements HistoryInterface
{
    protected $date;
    protected $status;
    protected $message;
	/**
     * @return the $date
     */
    public function getDate ()
    {
        return $this->date;
    }

	/**
     * @param field_type $date
     */
    public function setDate (\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

	/**
     * @return the $status
     */
    public function getStatus ()
    {
        return $this->status;
    }

	/**
     * @param field_type $status
     */
    public function setStatus (StatusInterface $status)
    {
        $this->status = $status;
        return $this;
    }

	/**
     * @return the $message
     */
    public function getMessage ()
    {
        return $this->message;
    }

	/**
     * @param field_type $message
     */
    public function setMessage ($message)
    {
        $this->message = $message;
        return $this;
    }

    
    
}

