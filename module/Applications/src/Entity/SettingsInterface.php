<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

namespace Applications\Entity;

use Settings\Entity\DisableElementsCapableFormSettings;
use Settings\Entity\ModuleSettingsContainer;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * General Settings for the application module.
 *
 * @ODM\EmbeddedDocument
 */
interface SettingsInterface
{
    /**
     * @param $mailAccess
     * @return $this
     */
    public function setMailAccess($mailAccess);

    public function getMailAccess();

    public function getAutoConfirmMail();


    /**
     * @param $formDisplaySkills
     * @return $this
     */
    public function setFormDisplaySkills($formDisplaySkills);

    /**
     * Gets the disabled apply form elements settings.
     *
     * @return DisableElementsCapableFormSettings
     */
    public function getApplyFormSettings();
}
