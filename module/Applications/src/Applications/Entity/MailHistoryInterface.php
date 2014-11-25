<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
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

