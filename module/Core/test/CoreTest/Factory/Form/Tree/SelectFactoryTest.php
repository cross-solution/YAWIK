<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Factory\Form\Tree;

use PHPUnit\Framework\TestCase;

use Core\Entity\Tree\Node;
use Core\Factory\Form\Tree\SelectFactory;
use Core\Form\Hydrator\Strategy\TreeSelectStrategy;
use CoreTest\Form\Hydrator\Strategy\TreeSelectStrategyTest;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill;
use Zend\ServiceManager\Factory\FactoryInterface;
//use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for \Core\Factory\Form\Tree\SelectFactory
 *
 * @covers \Core\Factory\Form\Tree\SelectFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @group Core
 * @group Core.Factory
 * @group Core.Factory.Form
 * @group Core.Factory.Form.Tree
 */
class SelectFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|SelectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        SelectFactory::class,
        '@testCreateServiceInvokesItself' => [
            'mock' => ['__invoke'],
        ],
    ];

    private $inheritance = [ FactoryInterface::class];

    public function testSetCreationOptions()
    {
        $options = [
            'test' => 'work?'
        ];

        $this->target->setCreationOptions($options);

        $this->assertAttributeEquals($options, 'options', $this->target);
    }

    public function testCreateServiceInvokesItself()
    {
        $services = new ServiceManager();
        $forms    = new FormElementManagerV3Polyfill($services);

        $options = [ 'test' => 'work?' ];
        $this->target->setCreationOptions($options);

        $this->target
            ->expects($this->once())
            ->method('__invoke')
            ->with($services, SelectFactory::class, $options)
            ->willReturn(true)
        ;

        $this->assertTrue($this->target->createService($services));
        $this->assertAttributeEmpty('options', $this->target);
    }

    public function provideMissingOptions()
    {
        return [
            [ null ],
            [ [] ],
            [ ['tree' => ['value' => 'test']] ],
            [ ['tree' => ['entity' => 'test']] ],
            [ ['tree' => ['name' => 'test']] ],
        ];
    }

    /**
     * @dataProvider provideMissingOptions
     *
     * @param $options
     */
    public function testInvokationThrowsExceptionIfOptionsMissing($options)
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('You must specify');

        $this->target->__invoke($this->getServiceManagerMock(), '', $options);
    }

    public function testInvokationThrowsExceptionIfRootNotFound()
    {
        $repository = $this
            ->getMockBuilder(\Doctrine\ODM\MongoDB\DocumentRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneBy'])
            ->getMock();
        $repository->expects($this->once())->method('findOneBy')->willReturn(null);

        $repositories = $this->createServiceManagerMock(['testEntityRepository' => $repository]);
        $services = $this->getServiceManagerMock(['repositories' => $repositories]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Tree root not found');

        $this->target->__invoke($services, '', ['tree' => [ 'entity' => 'testEntityRepository', 'value' => 'test']]);
    }

    private function getServiceContainer($criteria, $root=null)
    {
        if (null === $root) {
            $root = new Node('Test');
        }
        $repository = $this
            ->getMockBuilder(\Doctrine\ODM\MongoDB\DocumentRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneBy'])
            ->getMock();
        $repository->expects($this->once())->method('findOneBy')->with($criteria)->willReturn($root);

        $repositories = $this->createServiceManagerMock(['test' => $repository]);
        $services = $this->getServiceManagerMock(['repositories' => $repositories]);

        return $services;
    }

    public function provideCriteriaOptions()
    {
        return [
            [['tree' => ['entity' => 'test', 'value' => 'test']], ['value' => 'test']],
            [['tree' => ['entity' => 'test', 'name' => 'test']], ['name' => 'test']],
            [['tree' => ['entity' => 'test', 'name' => 'testName', 'value' => 'testValue']], ['value' => 'testValue']]
        ];
    }

    /**
     * @dataProvider provideCriteriaOptions
     *
     * @param $options
     * @param $criteria
     */
    public function testInvokationFetchesRootWithCorrectCriteria($options, $criteria)
    {
        $services = $this->getServiceContainer($criteria);

        $this->target->__invoke($services, '', $options);
    }

    private function createRoot($name, $children)
    {
        $root = new Node($name);

        foreach ($children as $childName => $grandChildren) {
            if (is_int($childName)) {
                $childName = $grandChildren;
                $grandChildren = [];
            }

            $root->addChild($this->createRoot($childName, $grandChildren));
        }

        return $root;
    }

    public function provideRootForValueOptions()
    {
        $root1 = $this->createRoot('root', ['child1' => ['gChild11', 'gChild12']]);
        $root2 = $this->createRoot('root', ['child1' => ['gChild11', 'gChild12']]);
        $root3 = $this->createRoot('root', ['child1' => ['gChild11', 'gChild12']]);

        return [
            [ $root1, [], ['child1' => ['label' => 'child1', 'options' => ['child1-gchild11' => 'gChild11', 'child1-gchild12' => 'gChild12']]]],
            [ $root2, ['use_root_item' => true], ['root' => ['label' => 'root', 'options' => ['child1' => ['label' => 'child1', 'options' => ['child1-gchild11' => 'gChild11', 'child1-gchild12' => 'gChild12']]]]]],
            [ $root3, ['allow_select_nodes' => true], ['child1-group' => ['label' => 'child1', 'options' => ['child1' => 'child1', 'child1-gchild11' => 'gChild11', 'child1-gchild12' => 'gChild12']]] ]
        ];
    }

    /**
     * @dataProvider provideRootForValueOptions
     *
     * @param $root
     * @param $options
     * @param $expect
     */
    public function testInvokationSetsCorrectValueOptions($root, $options, $expect)
    {
        $options = array_merge_recursive(['tree' => ['entity' => 'test', 'value' => 'test']], $options);
        $services = $this->getServiceContainer(['value' => 'test'], $root);

        $this->target->setCreationOptions($options);
        $select = $this->target->__invoke($services, '', $options);

        $this->assertEquals($expect, $select->getValueOptions());
    }

    public function testInvokationConfiguresSelectElement()
    {
        $root = new Node('test');
        $services = $this->getServiceContainer(['value' => 'test'], $root);
        $options = [
            'tree' => ['entity'=> 'test', 'value' => 'test'],
            'name' => 'testName',
            'options' => ['option1' => 'value1'],
            'attributes' => ['attribute1' => 'attribute1'],
        ];

        $select = $this->target->__invoke($services, '', $options);

        $this->assertEquals($options['name'], $select->getName(), 'Name not set.');
        $this->assertEquals($options['options'], $select->getOptions(), 'Options not set.');
        $this->assertArrayHasKey('attribute1', $select->getAttributes(), 'Attribute not set.');
        $this->assertInstanceOf(TreeSelectStrategy::class, $select->getHydratorStrategy());
        $this->assertSame($root, $select->getHydratorStrategy()->getTreeRoot());
        $this->assertAttributeInstanceOf(\Closure::class, 'allowSelectMultipleItems', $select->getHydratorStrategy());
        $this->assertFalse($select->getHydratorStrategy()->allowSelectMultipleItems());
        $select->setAttribute('multiple', 'multiple');
        $this->assertTrue($select->getHydratorStrategy()->allowSelectMultipleItems());
    }
}
