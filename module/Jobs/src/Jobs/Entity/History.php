<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** History.php */
namespace Jobs\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * History of an job. Holds status changes of an job opening
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
     * Status of a job opening.
     *
     * @var StatusInterface
     * @ODM\EmbedOne(targetDocument="status")
     */
    protected $status;
    
    /**
     * optional message, which can attached to a status change
     * @var String
     *
     * @ODM\Field(type="string")
     */
    protected $message;
    
    public function __construct($status, $message = '[System]')
    {
        if (!$status instanceof StatusInterface) {
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
     * Gets the date of an history entry
     *
     * @return $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the date of an history entry
     *
     * @param \DateTime $date
     * @return $this
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Gets the status of an history entry
     *
     * @return StatusInterface $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the Status of en history entry
     *
     * @param StatusInterface $status
     * @return $this
     */
    public function setStatus(StatusInterface $status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Gets the message of an history entry
     *
     * @return String $message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the message of an history entry
     *
     * @param String $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}
