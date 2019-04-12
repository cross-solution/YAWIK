<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Form\Tree;

use PHPUnit\Framework\TestCase;

use Core\Form\Hydrator\TreeHydrator;
use Core\Form\Tree\ManagementFieldset;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;
use CoreTest\Form\Hydrator\TreeHydratorTest;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use Zend\Form\Fieldset;

/**
 * Tests for \Core\Form\Tree\ManagementFieldset
 *
 * @covers \Core\Form\Tree\ManagementFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 * @group Core.Form.Tree
 */
class ManagementFieldsetTest extends TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait, TestDefaultAttributesTrait;

    /**
     *
     *
     * @var array|ManagementFieldset|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        ManagementFieldset::class,
        '@testInitialize' => [ 'mock' => ['add'] ],
    ];

    private $inheritance = [ Fieldset::class, ViewPartialProviderInterface::class ];

    private $traits = [ ViewPartialProviderTrait::class ];

    private $attributes = [
        'defaultPartial' => 'core/form/tree-manage',
    ];

    public function testInitialize()
    {
        $this->target->expects($this->once())
            ->method('add')->with([
                'type' => 'Collection',
                'name' => 'items',
                'options' => [
                    'count' => 0,
                    'should_create_template' => true,
                    'allow_add' => true,
                    'target_element' => [
                        'type' => 'Core/Tree/AddItemFieldset',
                    ],
                ],
            ]);

        $this->target->init();
    }

    public function testGetCorrectDefaultHydrator()
    {
        $expected = TreeHydrator::class;
        $actual   = $this->target->getHydrator();

        $this->assertInstanceOf($expected, $actual);
    }
}
