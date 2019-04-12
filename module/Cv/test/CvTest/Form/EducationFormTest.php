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
use Cv\Form\EducationForm;

/**
 * Tests for \Cv\Form\EducationForm
 *
 * @covers \Cv\Form\EducationForm
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 */
class EducationFormTest extends TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    /**
     *
     *
     * @var array|EducationForm
     */
    private $target = [
        EducationForm::class,
        '@testInitializesItself' => [
            'mock' => [
                'add',
                'setDescription' => [
                    'with' => 'Focus on the work experience that gives added weight to your application. Add separate entries for each course. Start from the most recent.',
                    'count' => 1,
                ],
                'setIsDescriptionsEnabled' => ['with' => true, 'count' => 1]],
        ],
    ];

    private $inheritance = [ SummaryForm::class ];

    private $attributes = [
        'baseFieldset' => 'EducationFieldset',
    ];

    public function testInitializesItself()
    {
        $this->target->init();
    }
}
