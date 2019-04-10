<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Form\Hydrator\Strategy;

use PHPUnit\Framework\TestCase;

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\Tree\AbstractLeafs;
use Core\Entity\Tree\AttachedLeafs;
use Core\Form\Hydrator\Strategy\TreeSelectStrategy;
use Core\Entity\Tree\Node;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Tests for \Core\Form\Hydrator\Strategy\TreeSelectStrategy
 *
 * @covers \Core\Form\Hydrator\Strategy\TreeSelectStrategy
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 * @group Core.Form.Hydrator
 * @group Core.Form.Hydrator.Strategy
 */
class TreeSelectStrategyTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    /**
     *
     *
     * @var string|TreeSelectStrategy
     */
    private $target = 'getTarget';

    private $inheritance = [ StrategyInterface::class ];

    public function propertiesProvider()
    {
        return [
            ['attachedLeafs', new ConcreteAbstractLeafs()],
            ['treeRoot', new Node()],
            ['allowSelectMultipleItems', ['default' => false, 'value' => true, 'getter_method' => '*']],
            ['allowSelectMultipleItems', ['value' => false, 'getter_method' => '*']],
            ['allowSelectMultipleItems', ['value' => function () {
                return true;
            }, 'getter_method' => '*', 'expect' => true]],

        ];
    }

    private function getTarget()
    {
        $target = new TreeSelectStrategy;
        $target->setTreeRoot($this->getTree());

        return $target;
    }

    private function getTree()
    {
        $root = new Node('root');
        $child1 = new Node('child1');
        $child2 = new Node('child2');
        $grandChild = new Node('grandChild');
        $grandChild2 = new Node('grandChild2');

        $child1->addChild($grandChild2);
        $child2->addChild($grandChild);

        $root->addChild($child1)->addChild($child2);

        return $root;
    }

    private function getAttachedLeafs()
    {
        $tree = $this->target->getTreeRoot();
        $children = $tree->getChildren();
        $item1 = $children->get(0);
        $grandchildren = $children->get(1)->getChildren();
        $item2 = $grandchildren->first();

        $leafs = new ConcreteAbstractLeafs();
        $leafs->setItems(new ArrayCollection([
                $item1, $item2
            ]));

        return $leafs;
    }

    public function testExctract()
    {
        $leafs = $this->getAttachedLeafs();
        $expect = 'child1';
        $this->assertEquals($expect, $this->target->extract($leafs), 'Single Select Extracting failed.');

        $expect = [
            'child1', 'child2-grandchild'
        ];

        $this->target->setAllowSelectMultipleItems(true);
        $this->assertEquals($expect, $this->target->extract($leafs), 'Multiple select extracting failed.');

        $this->assertSame($this->target->getAttachedLeafs(), $leafs, 'Extract does not set attached leafs.');
    }

    public function testExtractReturnUnchangedValueIfNoAbstractLeafIsPassed()
    {
        $this->assertEquals('unchanged', $this->target->extract('unchanged'));
    }

    public function hydrateTestProvider()
    {
        return [
            [null, 0],
            [[], 0],
            ['child2-grandchild', 1],
            [['child1', 'child2-grandchild'], 2]
        ];
    }

    /**
     * @dataProvider hydrateTestProvider
     *
     * @param $data
     * @param $count
     */
    public function testHydrate($data, $count)
    {
        $attachedLeafs = new ConcreteAbstractLeafs();
        $this->target->setAttachedLeafs($attachedLeafs);
        $this->target->setAllowSelectMultipleItems(is_array($data));

        $this->target->hydrate($data);

        $this->assertEquals($count, $attachedLeafs->getItems()->count());
    }
}

class ConcreteAbstractLeafs extends AbstractLeafs
{
}
