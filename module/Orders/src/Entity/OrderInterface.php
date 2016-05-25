<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\ModificationDateAwareEntityInterface;
use Doctrine\Common\Collections\Collection;
use Orders\Entity\Snapshot\SnapshotInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
interface OrderInterface extends EntityInterface, IdentifiableEntityInterface, ModificationDateAwareEntityInterface
{

    const TYPE_GENERAL = 'general';
    const TYPE_JOB     = 'job';

    /**
     * Sets the order number.
     *
     * @param $number
     *
     * @return self
     */
    public function setNumber($number);

    /**
     * Gets the order number.
     *
     * @return int
     */
    public function getNumber();

    /**
     * Sets the order type
     *
     * @param string $type
     *
     * @return self
     */
    public function setType($type);

    /**
     * Gets the order type
     *
     * @return string
     */
    public function getType();

    /**
     * Sets the snapshot entity.
     *
     * @param SnapshotInterface $entity
     *
     * @return self
     */
    public function setEntity(SnapshotInterface $entity);

    /**
     * Gets the snapshot entity.
     *
     * @return SnapshotInterface
     */
    public function getEntity();

    /**
     * Sets the invoice address.
     *
     * @param InvoiceAddressInterface $invoice
     *
     * @return self
     */
    public function setInvoiceAddress(InvoiceAddressInterface $invoiceAddress);

    /**
     * Gets the invoice address.
     *
     * @return InvoiceAddressInterface
     */
    public function getInvoiceAddress();

    /**
     * Sets the products collection.
     *
     * @param Collection $products
     *
     * @return self
     */
    public function setProducts(Collection $products);

    /**
     * Gets the products collection.
     *
     * @return Collection
     */
    public function getProducts();

    /**
     * Sets the currency.
     *
     * @param string $currency ISO 4217 code.
     *
     * @return self
     */
    public function setCurrency($currency);

    /**
     * Gets the currency.
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Sets the currency symbol.
     *
     * @param string $symbol
     *
     * @return self
     */
    public function setCurrencySymbol($symbol);

    /**
     * Gets the currency symbol.
     *
     * @return string
     */
    public function getCurrencySymbol();

    /**
     * Sets the tax rate.
     *
     * @param float $tax
     *
     * @return self
     */
    public function setTaxRate($tax);

    /**
     * Gets the tax rate.
     *
     * @return float
     */
    public function getTaxRate();

    /**
     * Gets the prices.
     *
     * @return array
     */
    public function getPrices();


}