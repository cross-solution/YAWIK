<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** HistoryInterface.php */
namespace Applications\Entity;

use Core\Entity\EntityInterface;

interface HistoryInterface extends EntityInterface
{
    public function setDate(\DateTime $date);
    public function getDate();
    
    public function setStatus(StatusInterface $status);
    public function getStatus();
    
    public function setMessage($message);
    public function getMessage();
}
