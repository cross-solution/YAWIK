<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** SettingsContainer.php */ 
namespace Settings\Entity;

use Core\Entity\EntityInterface;
/**
 * 
 */
interface SettingsContainerInterface extends EntityInterface
{
    public function get($key);
    public function set($key, $value);
    public function enableWriteAccess();
    public function toArray();
}

