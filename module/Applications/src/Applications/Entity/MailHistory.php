<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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
    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $mailText;

    /**
     * Get the mail subject of an history entry
     *
     * @return String $subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets the mail subject of an history entry
     *
     * @param String $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Gets the mail text of an history entry
     *
     * @return String $mailText
     */
    public function getMailText()
    {
        return $this->mailText;
    }

    /**
     * Sets the mail text of an history entry
     *
     * @param $mailText
     *
     * @return $this
     */
    public function setMailText($mailText)
    {
        $this->mailText = $mailText;
        return $this;
    }
}
