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

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\Tree\AttachedLeafs;
use Core\Form\Hydrator\Strategy\TreeSelectStrategy;
use CoreTest\Entity\Tree\ConcreteChildReference;

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
class TreeSelectStrategyTest extends \PHPUnit_Framework_TestCase
{
    private function getTree()
    {
        $root = new ConcreteChildReference('root');
        $child1 = new ConcreteChildReference('child1');
        $child2 = new ConcreteChildReference('child2');
        $grandChild = new ConcreteChildReference('grandChild');

        $child2->addChild($grandChild);

        $root->addChild($child1)->addChild($child2);

        return $root;
    }

    private function getAttachedLeafs()
    {
        $leafs = new ConcreteAttachedLeafs();
        $leafs->setItems(new ArrayCollection([
                new ConcreteChildReference('child1'),
                new ConcreteChildReference('grandChild')
            ]));

        return $leafs;
    }

    public function testExctract()
    {

        $target = new TreeSelectStrategy();

        $target->setTreeRoot($this->getTree());

        $expect = [
            'child1', 'grandchild'
        ];

        $this->assertEquals($expect, $target->extract($this->getAttachedLeafs()));

    }

    public function testHydrate()
    {
        $target = new TreeSelectStrategy();

        $target->setTreeRoot($this->getTree());

        $attachedLeafs = new ConcreteAttachedLeafs();
        $target->setAttachedLeafs($attachedLeafs);

        $data = [ 'child1', 'grandchild' ];

        $target->hydrate($data);

        $this->assertEquals(2, $attachedLeafs->getItems()->count());

    }
}

class ConcreteAttachedLeafs extends AttachedLeafs
{

}