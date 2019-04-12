<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Form\SummaryForm;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Form\ClassificationsForm;

/**
 * Tests for Jobs\Form\ClassificationsForm
 *
 * @covers \Jobs\Form\ClassificationsForm
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class ClassificationsFormTest extends TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    private $target = ClassificationsForm::class;

    private $inheritance = [SummaryForm::class];

    private $attributes = [
        'baseFieldset' => 'Jobs/ClassificationsFieldset',
        'label' => 'Classifications',
    ];
}
