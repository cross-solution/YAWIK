<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Form\Hydrator;

use PHPUnit\Framework\TestCase;

use Core\Form\Hydrator\TreeHydrator;
use Core\Entity\Tree\Node;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\Hydrator\HydratorInterface;

/**
 * Tests for \Core\Form\Hydrator\TreeHydrator
 *
 * @covers \Core\Form\Hydrator\TreeHydrator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class TreeHydratorTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|TreeHydrator|\ReflectionClass
     */
    private $target = TreeHydrator::class;

    private $inheritance = [ HydratorInterface::class ];

    private function getHydratedTree($mode = 'all')
    {
        $root = new Node();
        $root->setId('root-id')->setName('Root')->setValue('root')->setPriority(1);

        $child1 = new Node();
        $child1->setId('child-one-id')->setName('ChildOne')->setValue('child-one')->setPriority(1);

        $root->addChild($child1);

        if ('all' == $mode || 'no-ids' == $mode) {
            $child2 = new Node();
            if ('all' == $mode) {
                $child2->setId('child-two-id');
            }
            $child2->setName('ChildTwo')->setValue('child-two')->setPriority(2);

            $grandchild1 = new Node();
            if ('all' == $mode) {
                $grandchild1->setId('grand-child-one-id');
            }
            $grandchild1->setName('GrandChildOne')->setValue('grand-child-one')->setPriority(1);

            $child2->addChild($grandchild1);

            $root->addChild($child2);
        }

        return $root;
    }

    private function getExtractedTree()
    {
        return [ 'items' => [
            new \ArrayObject([
                'id' => 'root-id',
                'current' => '1',
                'name' => 'Root',
                'value' => 'root',
                'do' => 'nothing',
                'priority' => 1,
            ]),
            new \ArrayObject([
                'id' => 'child-one-id',
                'current' => '1-1',
                'name' => 'ChildOne',
                'value' => 'child-one',
                'do' => 'nothing',
                'priority' => 1,
            ]),
            new \ArrayObject([
                'id' => 'child-two-id',
                'current' => '1-2',
                'name' => 'ChildTwo',
                'value' => 'child-two',
                'do' => 'nothing',
                'priority' => 2,
            ]),
            new \ArrayObject([
                'id' => 'grand-child-one-id',
                'current' => '1-2-1',
                'name' => 'GrandChildOne',
                'value' => 'grand-child-one',
                'do' => 'nothing',
                'priority' => 1
            ]),
        ]];
    }

    public function testExtractingTree()
    {
        $this->assertEquals($this->getExtractedTree(), $this->target->extract($this->getHydratedTree()));
    }

    public function testHydrating()
    {
        $expected = $this->getHydratedTree('no-ids');
        $object   = $this->getHydratedTree('partial');
        $data     = $this->getExtractedTree();
        $data['items'][2]['do'] = 'set';
        $data['items'][3]['do'] = 'set';
        $this->assertEquals($expected, $this->target->hydrate($data, $object));
    }

    public function testHydratingRemovesElement()
    {
        $expected = $this->getHydratedTree();
        $children = $expected->getChildren();
        $expected->removeChild($children[1]);

        $object = $this->getHydratedTree();

        $data = $this->getExtractedTree();
        $data['items'][2]['do'] = 'remove';

        $this->assertEquals($expected, $this->target->hydrate($data, $object));
    }
}
