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

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * A product
 *
 * @ODM\EmbeddedDocument
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Product implements ProductInterface
{

    /**
     * The name of the product.
     *
     * @ODM\String
     * @var string
     */
    protected $name;

    /**
     * The product number
     *
     * @ODM\String
     * @var string
     */
    protected $productNumber;

    /**
     * The price
     *
     * @ODM\Field(type="float")
     * @var float
     */
    protected $price = 0;

    /**
     * The quantity
     *
     * @ODM\Field(type="int")
     * @var int
     */
    protected $quantity = 1;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setProductNumber($number)
    {
        $this->productNumber = $number;

        return $this;
    }

    public function getProductNumber()
    {
        return $this->productNumber;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }


    
}