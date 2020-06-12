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
