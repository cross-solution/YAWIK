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
use Cv\Form\LanguageSkillForm;

/**
 * Tests for \Cv\Form\LanguageSkillForm
 *
 * @covers \Cv\Form\LanguageSkillForm
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 */
class LanguageSkillFormTest extends TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    /**
     *
     *
     * @var array|LanguageSkillForm
     */
    private $target = [
        LanguageSkillForm::class,
        '@testInitializesItself' => [
            'mock' => [
                'add',
                'setDescription' => [
                    'with' => 'Please select a language and self-assess your level',
                    'count' => 1,
                ],
                'setIsDescriptionsEnabled' => ['with' => true, 'count' => 1]],
        ],
    ];

    private $inheritance = [ SummaryForm::class ];

    private $attributes = [
        'baseFieldset' => 'Cv/LanguageSkillFieldset',
    ];

    public function testInitializesItself()
    {
        $this->target->init();
    }
}
