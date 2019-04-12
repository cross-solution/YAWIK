<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Entity\Tree;

use PHPUnit\Framework\TestCase;

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\EntityTrait;
use Core\Entity\Tree\AbstractLeafs;
use Core\Entity\Tree\LeafsInterface;
use Core\Entity\Tree\Node;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;

/**
 * Tests for \Core\Entity\Tree\AbstractLeafs
 *
 * @covers \Core\Entity\Tree\AbstractLeafs
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Tree
 */
class AbstractLeafsTest extends TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait, TestSetterGetterTrait;

    /**
     *
     *
     * @var array|\ReflectionClass|AbstractLeafs
     */
    private $target = [
        ConcreteAbstractLeafs::class,
        '@testInheritance' => '#reflection',
        '@testUsesTraits' => '#reflection',
        '#reflection' => [
            AbstractLeafs::class,
            'as_reflection' => true,
        ]
    ];

    private $inheritance = [ LeafsInterface::class ];

    private $traits = [ EntityTrait::class ];

    public function propertiesProvider()
    {
        return [
            [ 'items', ['default' => new ArrayCollection(), 'value' => new \Doctrine\Common\Collections\ArrayCollection()]],
        ];
    }

    public function testToString()
    {
        $root = new Node('root');
        $child1 = new Node('child1');
        $child2 = new Node('child2');
        $gChild1 = new Node('grandchild1');

        $child2->addChild($gChild1);
        $root->addChild($child1)->addChild($child2);

        $targetItems = $this->target->getItems();
        $targetItems->add($gChild1);
        $targetItems->add($child1);

        $expect = 'child2 | grandchild1, child1';

        $this->assertEquals($expect, $this->target->__toString());
    }

    public function testUpdateValues()
    {
        $root = new Node('root');
        $child1 = new Node('child1');
        $child2 = new Node('child2');
        $gChild1 = new Node('grandchild1');

        $child2->addChild($gChild1);
        $root->addChild($child1)->addChild($child2);

        $targetItems = $this->target->getItems();
        $targetItems->add($gChild1);
        $targetItems->add($child1);

        $this->target->updateValues();
        $this->assertEquals(['child2-grandchild1', 'child1'], $this->target->getValues());
    }
}

class ConcreteAbstractLeafs extends AbstractLeafs
{
}
