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

/**
 *
 */
interface ModuleSettingsContainerInterface extends SettingsContainerInterface
{
    public function getModuleName();
}
