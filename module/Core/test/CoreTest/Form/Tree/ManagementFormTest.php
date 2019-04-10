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

use Core\Form\HeadscriptProviderInterface;
use Core\Form\SummaryForm;
use Core\Form\Tree\ManagementForm;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Tests for \Core\Form\Tree\ManagementForm
 *
 * @covers \Core\Form\Tree\ManagementForm
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 * @group Core.Form.Tree
 */
class ManagementFormTest extends TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait, TestSetterGetterTrait;

    private $target = ManagementForm::class;

    private $inheritance = [ SummaryForm::class, HeadscriptProviderInterface::class ];

    private $attributes = [
        'baseFieldset' => 'Core/Tree/ManagementFieldset',
        'attributes'   => [ 'method' => 'POST', 'class' => 'yk-tree-management-form' ],
    ];

    private $properties = [
        [ 'headscripts', ['value' => ['testHeadScript'], 'default' => [ 'modules/Core/js/html.sortable.min.js', 'modules/Core/js/forms.tree-management.js' ]] ]
    ];
}
