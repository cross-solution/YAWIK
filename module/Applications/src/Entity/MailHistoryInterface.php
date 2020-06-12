<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** HistoryInterface.php */
namespace Applications\Entity;

interface MailHistoryInterface extends HistoryInterface
{
    public function setSubject($subject);
    public function getSubject();

    public function setMailText($text);
    public function getMailText();
}
