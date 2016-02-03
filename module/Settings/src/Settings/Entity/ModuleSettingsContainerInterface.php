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

/**
 *
 */
interface ModuleSettingsContainerInterface extends SettingsContainerInterface
{
    public function getModuleName();
}
