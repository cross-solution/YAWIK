<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Form\SummaryForm;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Form\CustomerNote;

/**
 * Tests for \Jobs\Form\CustomerNote
 *
 * @covers \Jobs\Form\CustomerNote
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class CustomerNoteTest extends TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    private $target = CustomerNote::class;

    private $inheritance = [ SummaryForm::class ];

    private $attributes = [
        'baseFieldset' => 'Jobs/CustomerNoteFieldset',
        'label' => 'Customer note',
        'displayMode' => SummaryForm::DISPLAY_SUMMARY,
    ];
}
