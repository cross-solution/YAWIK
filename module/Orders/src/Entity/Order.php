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
use Core\Entity\EntityTrait;
use Core\Entity\ImmutableEntityInterface;
use Core\Entity\ImmutableEntityTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\IdentifiableEntityTrait;
use Core\Entity\ModificationDateAwareEntityTrait;
use Doctrine\ODM\MongoDB\Proxy\Proxy;
use Orders\Entity\Snapshot\SnapshotInterface;

/**
 * Order entity
 *
 * @ODM\Document(collection="orders", repositoryClass="Orders\Repository\Orders")
 * @ODM\HasLifeCycleCallbacks
 * @ODM\Indexes({
 *      @ODM\Index(keys={
 *                  "number"="text",
 *                  "invoiceAddress.name"="text",
 *                    "invoiceAddress.company"="text",
 *                    "invoiceAddress.street"="text",
 *                    "invoiceAddress.zipCode"="text",
 *                    "invoiceAddress.city"="text",
 *                     "invoiceAddress.region"="text",
 *                     "invoiceAddress.country"="text",
 *                     "invoiceAddress.vatId"="text",
 *                     "invoiceAddress.email"="text"
 *                 }, name="fulltext")
 * })
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Order implements OrderInterface, ImmutableEntityInterface
{
    use EntityTrait, IdentifiableEntityTrait, ModificationDateAwareEntityTrait, ImmutableEntityTrait;


    /**
     * The order number
     *
     * @ODM\String
     * @var string
     */
    protected $number;

    /**
     * Order type.
     * Used for filtering and organization issues.
     *
     * @ODM\String
     * @var string
     */
    protected $type = self::TYPE_GENERAL;

    /**
     * The snapshot entity.
     *
     * @ODM\EmbedOne(discriminatorField="_entity")
     * @var SnapshotInterface
     */
    protected $entity;

    /**
     * The invoice address.
     *
     * @ODM\EmbedOne(targetDocument="\Orders\Entity\InvoiceAddress")
     * @var InvoiceAddressInterface
     */
    protected $invoiceAddress;

    /**
     * The products of this order.
     *
     * @ODM\EmbedMany(discriminatorField="_entity")
     * @var Collection
     */
    protected $products;

    /**
     * Currency (ISO 4217)
     *
     * @ODM\String
     * @var string
     */
    protected $currency;

    /**
     * Currency symbol.
     *
     * @ODM\String
     * @var string
     */
    protected $currencySymbol;

    /**
     * The tax rate.
     *
     * @ODM\Field(type="float")
     * @var float
     */
    protected $taxRate = 0;

    /**
     * The prices of this order.
     *
     * It's an array with three fields:
     * <pre>
     * [
     *      "products" => [
     *          <product_name> => [
     *              'single_total' => total price of ONE product incl. tax,
     *              'single_tax'   => tax of ONE product,
     *              'single_pretax' => total price excl. tax
     *              'total' => total price incl. tax of ALL quantities of the product.
     *              'tax' => total tax of ALL quantities,
     *              'pretax' => total price excl. tax of ALL quantities.
     *          ],
     *          ...
     *      ],
     *      "total"  => total amount incl. tax
     *      "tax"    => amount of the tax
     *      "pretax" => total amount excl. tax
     * ]
     * if this entity is not yet persisted, prices are
     * calculated each time on access.
     *
     * @ODM\Hash
     * @var array
     */
    protected $prices;

    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    public function setEntity(SnapshotInterface $entity)
    {
        $this->entity = $entity;

        return $this;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrencySymbol($currencySymbol)
    {
        $this->currencySymbol = $currencySymbol;

        return $this;
    }

    public function getCurrencySymbol()
    {
        return $this->currencySymbol;
    }

    public function setInvoiceAddress(InvoiceAddressInterface $invoiceAddress)
    {
        $this->invoiceAddress = $invoiceAddress;

        return $this;
    }

    public function getInvoiceAddress()
    {
        return $this->invoiceAddress;
    }

    public function setProducts(Collection $products)
    {
        $this->products = $products;

        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * Sets the total amount without tax.
     *
     * Discount and sconti should be calculated before hand.
     *
     * @param int $amount
     *
     * @return self
     */
    public function setPrice($amount)
    {
        $tax    = $this->getTaxRate()  / 100;
        $taxAmount = $amount * $tax;

        $this->prices = [
            'pretax' => round($amount, 2),
            'tax'    => round($taxAmount, 2),
            'net'    => round($amount + $taxAmount, 2),
        ];

        return $this;
    }

    public function getPrice($type="net")
    {
        if (!$this->prices || !array_key_exists($type, $this->prices)) {
            return 0;
        }

        return $this->prices[$type];
    }

    /**
     * Gets the calculated prices.
     *
     * @param bool $calculate Should the prices be recalculated.
     *
     * @return array
     */
    public function getPrices($calculate=false)
    {
        /*if (!$this->prices || $calculate) {
            $this->calculatePrices();
        }*/

        return $this->prices;
    }

    /**
     * Calculates the prices.
     *
     * Not used at the moment.
     */
    public function calculatePrices()
    {
        if ($this->getId()) {
            return;
        }

        $taxFactor = $this->getTaxRate() / 100;
        $total = $pretax = $tax = 0;
        $sums = [];

        /* @var ProductInterface $product */
        foreach ($this->getProducts() as $product) {
            $pPreTax = $product->getPrice();
            $pTax    = $pPreTax * $taxFactor;
            $pTotal  = $pPreTax + $pTax;
            $pQuantity = $product->getQuantity();
            $ptPreTax = $pQuantity * $pPreTax;
            $ptTax    = $pQuantity * $pTax;
            $ptTotal  = $pQuantity * $pTotal;

            $sums[$product->getName()] = [
                'single_pretax' => $pPreTax,
                'single_tax'    => $pTax,
                'single_total'  => $pTotal,
                'pretax' => $ptPreTax,
                'tax'    => $ptTax,
                'total'  => $ptTotal,
            ];

            $total += $ptTotal;
            $pretax += $ptPreTax;
            $tax   += $ptTax;
        }

        $this->prices = [
            'products' => $sums,
            'total'  => $total,
            'tax'    => $tax,
            'pretax' => $pretax,
        ];
    }
}