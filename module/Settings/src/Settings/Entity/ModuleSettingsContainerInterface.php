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


/**
 *
 */
interface ModuleSettingsContainerInterface extends SettingsContainerInterface
{
    public function getModuleName();
}

