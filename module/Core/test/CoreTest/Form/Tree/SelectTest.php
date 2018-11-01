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

use Core\Form\Hydrator\HydratorStrategyProviderInterface;
use Core\Form\Hydrator\HydratorStrategyProviderTrait;
use Core\Form\Tree\Select;
use Core\Form\Element\Select as ZfSelect;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;

/**
 * Tests for \Core\Form\Tree\Select
 * 
 * @covers \Core\Form\Tree\Select
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 * @group Core.Form.Tree
 */
class SelectTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait;

    private $target = Select::class;

    private $inheritance = [ ZfSelect::class, HydratorStrategyProviderInterface::class ];

    private $traits = [ HydratorStrategyProviderTrait::class ];
}