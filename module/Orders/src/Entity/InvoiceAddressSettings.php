<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** */
namespace Orders\Entity;

use Core\Entity\Hydrator\EntityHydrator;
use Settings\Entity\SettingsContainer as Container;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class InvoiceAddressSettings extends Container
{
    /**
     * Form of address.
     *
     * @ODM\String
     * @var string
     */
    protected $gender;

    /**
     * Full name
     *
     * @ODM\String
     * @var string
     */
    protected $name;

    /**
     * Company name.
     *
     * @ODM\String
     * @var string
     */
    protected $company;

    /**
     * Street name and house number.
     *
     * @ODM\String
     * @var string
     */
    protected $street;

    /**
     * Zip code.
     *
     * @ODM\String
     * @var string
     */
    protected $zipCode;

    /**
     * City name.
     *
     * @ODM\String
     * @var string
     */
    protected $city;

    /**
     * Region.
     *
     * @ODM\String
     * @var string
     */
    protected $region;

    /**
     * Country.
     *
     * @ODM\String
     * @var string
     */
    protected $country;

    /**
     * Value added tax identification number.
     *
     * @ODM\String
     * @var string
     */
    protected $vatId;

    /**
     * Email address.
     *
     * @ODM\String
     * @var string
     */
    protected $email;

    public function getInvoiceAddressEntity()
    {
        $hydrator = new EntityHydrator();
        $data     = $this->getSettings();
        $entity   = $hydrator->hydrate($data, new InvoiceAddress());

        return $entity;
    }
}
