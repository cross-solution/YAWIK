<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace OrdersTest\Entity;

use Orders\Entity\Product;

/**
 * Tests for Product
 *
 * @covers \Orders\Entity\Product
 * @coversDefaultClass \Orders\Entity\Product
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Orders
 * @group  Orders.Entity
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Product
     */
    private $target;

    public function setup()
    {
        $this->target = new Product();
    }

    /**
     * @coversNothing
     */
    public function testInstanceOfProduct()
    {
        $this->assertInstanceOf('\Orders\Entity\ProductInterface', $this->target);
    }


    /**
     * @testdox Allows setting and getting the URI
     */
    public function testSettingAndGettingName()
    {
        $input = 'Product Name';

        $this->target->setName($input);

        $this->assertEquals($input, $this->target->getName());
    }

    /**
     * @testdox Allows setting and getting the URI
     */
    public function testSettingAndGettingPrice()
    {
        $input = 100.59;

        $this->target->setPrice($input);

        $this->assertEquals($input, $this->target->getPrice());
    }

    /**
     * @testdox Allows setting and getting the URI
     */
    public function testSettingAndGettingProductNumber()
    {
        $input = "12ABC";

        $this->target->setProductNumber($input);

        $this->assertEquals($input, $this->target->getProductNumber());
    }

    /**
     * @testdox Allows setting and getting the URI
     */
    public function testSettingAndGettingQuantity()
    {
        $input = 10;

        $this->target->setQuantity($input);

        $this->assertEquals($input, $this->target->getQuantity());
    }

}
