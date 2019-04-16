<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Form;

use PHPUnit\Framework\TestCase;

use Applications\Form\ApplicationsFilter;
use Applications\Form\Element\JobSelect;
use Core\Form\SearchForm;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;

/**
 * Tests for \Applications\Form\ApplicationsFilter
 *
 * @covers \Applications\Form\ApplicationsFilter
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Applications
 * @group Applications.Form
 */
class ApplicationsFilterTest extends TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|ApplicationsFilter
     */
    private $target = [
        ApplicationsFilter::class,
        '@testAddElements' => ['mock' => ['add', 'setButtonElement' => ['with' => 'unread', 'count' => 1]]],
    ];

    private $inheritance = [ SearchForm::class ];

    private $attributes = [
        'options' => [
            'name' => 'applications_filter',
            'column_map' => [
                'q' => 3,
                'job' => 3,
                'status' => 2,
                'unread' => 3,
            ],
            'buttons_span' => 3.
        ]
    ];

    public function testAddElements()
    {
        $add1 = [
            'type' => JobSelect::class,
            'name' => 'job',
            'options' => [
                'label' => 'Enter job title',
            ],
            'attributes' => [
                'id' => 'job-filter',
                'class' => 'form-control',
                'data-placeholder' => 'Enter job title',
                'data-autoinit' => 'false',
                'data-submit-on-change' => 'true',
            ]
        ];

        $add2 = [
            'type' => 'Applications\Form\Element\StatusSelect',
            'name' => 'status',
            'options' => [
                'label' => 'Status',
            ],
            'attributes' => [
                'data-width' => '100%',
                'data-submit-on-change' => 'true',
                'data-placeholder' =>  'all',
            ]
        ];

        $add3 = ['type' => 'ToggleButton',
                 'name' => 'unread',
                 'options' => [
                     'checked_value' => '1',
                     'unchecked_value' => '0',
                     'label' => 'unread',
                 ],
                 'attributes' => [
                     'data-submit-on-change' => 'true',
                 ]
        ];

        /** @noinspection PhpUndefinedMethodInspection */
        $this->target->expects($this->exactly(4))->method('add')
            ->withConsecutive(
                [$this->anything()],
                [$add1],
                [$add2],
                [$add3]
            );

        $this->target->init();
    }
}
