<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
    public function getSettings();
    
    public function set($key, $value);
    public function setSettings(array $settings);
    
    public function enableWriteAccess();
}
