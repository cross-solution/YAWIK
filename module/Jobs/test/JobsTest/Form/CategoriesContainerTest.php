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

use Core\Form\SummaryForm;
use Core\Form\WizardContainer;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Form\CategoriesContainer;

/**
 * Tests for \Jobs\Form\CategoriesContainer
 * 
 * @covers \Jobs\Form\CategoriesContainer
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class CategoriesContainerTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    private $target = [
        CategoriesContainer::class,
        '@testInitialize' => [
            'mock' => ['setForms'],
        ],
    ];

    private $inheritance = [ WizardContainer::class ];

    public function testInitialize()
    {
        $this->target->expects($this->once())->method('setForms')
            ->with([
            'professions' => [
                'options' => [
                    'label' => 'Professions',
                ],
                'entity' => 'professions',
                'property' => true,
                'forms' => [
                    'professions' => [
                        'type' => 'Core/Tree/Management',
                        'property' => true,
                        'options' => [
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'Manage the professions you want to assign to jobs.',
                            'display_mode' => SummaryForm::DISPLAY_SUMMARY,
                        ],
                    ],
                ],
            ],
            'employmentTypes' => [
                'options' => [
                    'label' => 'Employment Types',
                ],
                'entity' => 'employmentTypes',
                'property' => true,
                'forms' => [
                    'employmentTypes' => [
                        'type' => 'Core/Tree/Management',
                        'property' => true,
                        'options' => [
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'Manage the employment types you want to assign to jobs.',
                            'display_mode' => SummaryForm::DISPLAY_SUMMARY,
                        ],
                    ],
                ],
            ],
        ]);

        $this->target->init();
    }
}