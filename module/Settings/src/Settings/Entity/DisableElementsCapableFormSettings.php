<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright 2013-2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Settings\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Settings container for storing disabled form elements.
 *
 * @method array getDisableElements()
 * @method DisableElementsCapableFormSettings setForm()
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @ODM\EmbeddedDocument
 */
class DisableElementsCapableFormSettings extends SettingsContainer
{
    /**
     * The service name of the target form.
     *
     * @ODM\String
     * @var string
     */
    protected $form;

    /**
     * The array of the disabled elements.
     *
     * @ODM\Hash(nullable = true)
     * @var array
     * @see \Core\Form\DisableElementsCapableInterface::disableElements()
     */
    protected $disableElements;

    /**
     * Should this form settings considered active?
     *
     * @ODM\Boolean
     * @var bool
     */
    protected $isActive = false;

    /**
     * Gets if this form settings should be considered active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }
}