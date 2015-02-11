<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
     * @var StatusInterface
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
    
    public function __construct($status, $message='[System]')
    {
        if (!$status instanceOf StatusInterface) {
            $status = new Status($status);
        }
        $this->setStatus($status);
        $this->setMessage($message);
        $this->setDate(new \DateTime());
    }
    
    public function preUpdate()
    {
        if (!$this->date) {
            $this->setDate(new \DateTime());
        }
    }
    
	/**
     * @return $date
     */
    public function getDate ()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setDate (\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

	/**
     * @return StatusInterface $status
     */
    public function getStatus ()
    {
        return $this->status;
    }

	/**
     * @param StatusInterface $status
     */
    public function setStatus (StatusInterface $status)
    {
        $this->status = $status;
        return $this;
    }

	/**
     * @return String $message
     */
    public function getMessage ()
    {
        return $this->message;
    }

	/**
     * @param String $message
     */
    public function setMessage ($message)
    {
        $this->message = $message;
        return $this;
    }
}

