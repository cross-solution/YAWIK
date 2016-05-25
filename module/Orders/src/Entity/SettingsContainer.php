<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Orders\Entity;

use Settings\Entity\ModuleSettingsContainer;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Settings\Entity\InitializeAwareSettingsContainerInterface;

/**
 * @ODM\EmbeddedDocument
 */
class SettingsContainer extends ModuleSettingsContainer implements InitializeAwareSettingsContainerInterface
{
    
    /**
     * @ODM\EmbedOne(targetDocument="InvoiceAddressSettings")
     */
    protected $invoiceAddress;

    /**
     * Initialize the settings container
     */
    public function init()
    {
        $this->getInvoiceAddress();
    }

    /**
     * Get localization settings
     *
     * @return LocalizationSettings
     */
    public function getInvoiceAddress()
    {
        if (!$this->invoiceAddress) {
            $this->invoiceAddress = new InvoiceAddressSettings();
        }
        return $this->invoiceAddress;
    }
}
