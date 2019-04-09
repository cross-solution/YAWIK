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
use Cv\Form\EmploymentForm;

/**
 * Tests for \Cv\Form\EmploymentForm
 *
 * @covers \Cv\Form\EmploymentForm
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 */
class EmploymentFormTest extends TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    /**
     *
     *
     * @var array|EmploymentForm
     */
    private $target = [
        EmploymentForm::class,
        '@testInitializesItself' => [
            'mock' => [
                'add',
                'setDescription' => [
                    'with' => ' Focus on the work experience that gives added weight to your application.<br>Add separate entries for each experience. Start with the most recent.<br>If your work experience is limited:<ul><li>describe your education and training first</li><li>mention volunteering or (paid/unpaid) work placements which provide evidence of work experience.</li></ul>',
                    'count' => 1,
                ],
                'setIsDescriptionsEnabled' => ['with' => true, 'count' => 1]],
        ],
    ];

    private $inheritance = [ SummaryForm::class ];

    private $attributes = [
        'baseFieldset' => 'EmploymentFieldset',
    ];

    public function testInitializesItself()
    {
        $this->target->init();
    }
}
