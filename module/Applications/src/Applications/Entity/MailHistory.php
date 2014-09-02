<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** MailHistory.php */ 
namespace Applications\Entity;

/**
 * Holds a history of sent mails.
 *
 * Class MailHistory
 * @package Applications\Entity
 */
class MailHistory extends History implements MailHistoryInterface
{
    protected $subject;
    protected $mailText;
	/**
     * @return String $subject
     */
    public function getSubject ()
    {
        return $this->subject;
    }

    /**
     * @param String $subject
     * @return $this
     */
    public function setSubject ($subject)
    {
        $this->subject = $subject;
        return $this;
    }

	/**
     * @return String $mailText
     */
    public function getMailText ()
    {
        return $this->mailText;
    }

	/**
     * @param String $mailText
     */
    public function setMailText ($mailText)
    {
        $this->mailText = $mailText;
        return $this;
    }

    
    
}

