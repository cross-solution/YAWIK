<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
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

