<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
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
