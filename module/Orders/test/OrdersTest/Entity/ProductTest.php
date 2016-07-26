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

use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Orders\Entity\Product;
use Orders\Entity\ProductInterface;

/**
 * Tests for Product
 *
 * @covers \Orders\Entity\Product
 * @coversDefaultClass \Orders\Entity\Product
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group  Orders
 * @group  Orders.Entity
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    /**
     * The "Class under Test"
     *
     * @var Product
     */
    private $target = Product::class;

    /**
     * @see TestInheritanceTrait
     *
     * @var array
     */
    private $inheritance = [ ProductInterface::class ];

    /**
     * @see TestSetterGetterTrait
     *
     * @var array
     */
    private $properties = [
        [ 'name', 'Product Name' ],
        [ 'price', 100.59 ],
        [ 'productNumber', '12ABC' ],
        [ 'quantity', 10 ],
    ];
}
