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

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
interface ProductInterface 
{
    /**
     * Sets the product name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * Gets the product name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the product number.
     *
     * @param string $number
     *
     * @return self
     */
    public function setProductNumber($number);

    /**
     * Gets the product number.
     *
     * @return string
     */
    public function getProductNumber();

    /**
     * Sets the price (without tax).
     *
     * Currency and tax is handled in the order.
     *
     * @param float $price
     *
     * @return self
     */
    public function setPrice($price);

    /**
     * Gets the price (without tax).
     *
     * @return float
     */
    public function getPrice();

    /**
     * Sets the quantity.
     *
     * @param int $count
     *
     * @return self
     */
    public function setQuantity($count);

    /**
     * Gets the quantity.
     *
     * @return int
     */
    public function getQuantity();
}