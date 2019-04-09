<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Form\SummaryForm;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Form\NativeLanguageForm;

/**
 * Tests for \Cv\Form\NativeLanguage
 *
 * @covers \Cv\Form\NativeLanguage
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 */
class NativeLanguageFormTest extends TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    private $target = NativeLanguageForm::class;

    private $inheritance = [ SummaryForm::class ];

    private $attributes = [
        'baseFieldset' => 'Cv/NativeLanguageFieldset'
    ];
}
