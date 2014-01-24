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
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * History of an application. Holds status changes of an application
 * 
 * @ODM\EmbeddedDocument
 */
class History extends AbstractEntity implements HistoryInterface
{
    /**
     * @ODM\Field(type="tz_date")
     */
    protected $date;
    
    /**
     * Status of an application.
     * 
     * @var unknown
     * @ODM\EmbedOne(targetDocument="status")
     */
    protected $status;
    
    /**
     * optional message, which can attached to a status change
     * @var String
     * 
     * @ODM\String
     */
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

