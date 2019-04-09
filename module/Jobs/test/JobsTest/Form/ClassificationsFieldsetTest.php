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

use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\Tree\Select;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Jobs\Entity\Category;
use Jobs\Form\ClassificationsFieldset;
use Zend\Form\Fieldset;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill;
use Zend\Hydrator\Strategy\DefaultStrategy;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Tests for \Jobs\Form\ClassificationsFieldset
 *
 * @covers \Jobs\Form\ClassificationsFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class ClassificationsFieldsetTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = [
        ClassificationsFieldset::class,
        '@testInitialize' => [
            'mock' => ['setName' => ['with' => 'classifications'], 'add' ],
        ],
    ];

    private $inheritance = [ Fieldset::class ];

    private $properties = [
        ['hydrator', ['ignore_setter' => true, 'ignore_getter' => true, 'default@' => EntityHydrator::class ]],
    ];

    public function testInitialize()
    {
        $select = new Select();
        $strategy = new DefaultStrategy();
        $select->setHydratorStrategy($strategy);
        $formElements = $this
            ->getMockBuilder(FormElementManagerV3Polyfill::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock()
        ;
        $formElements
            ->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                [
                        'Core/Tree/Select',
                        [
                            'tree' => [
                                'entity' => Category::class,
                                'value' => 'professions',
                            ],
                            'allow_select_nodes' => true,
                            'name' => 'professions',
                            'options' => [
                                'description' =>  'Select the professions of the job opening. This allows an applicant to find job openings by a certain profession.',
                                'label' =>  'Professions',
                            ],
                            'attributes' => [
                                'data-width' => '100%',
                                'multiple' => true,
                            ],
                        ]
                    ],
                [
                        'Core/Tree/Select',
                        [
                            'tree' => [
                                'entity' => Category::class,
                                'value' => 'industries',
                            ],
                            'allow_select_nodes' => true,
                            'name' => 'industries',
                            'options' => [
                                'description' => 'Select the industry of the hiring organization. This allows an applicant to search for job opening by industry.',
                                'label' =>  'Industries',
                            ],
                            'attributes' => [
                                'data-width' => '100%',
                                'multiple' => true,
                            ],
                        ]
                    ],
                [
                        'Core/Tree/Select',
                        [
                            'tree' => [
                                'entity' => Category::class,
                                'value' => 'employmentTypes',
                            ],
                            'name' => 'employmentTypes',
                            'options' => [
                                'label' =>  'Employment Types',
                                'description' => 'Manage the employment types you want to assign to jobs.',
                            ],
                            'attributes' => [
                                'data-width' => '100%',
                                'multiple' => true,
                            ]
                        ]
                    ]
            )
            ->willReturn($select)
        ;
        $this->target->expects($this->exactly(3))->method('add')->with($select);

        $this->target->getFormFactory()->setFormElementManager($formElements);

        $hydrator = $this->getMockBuilder(EntityHydrator::class)->disableOriginalConstructor()
            ->setMethods(['addStrategy'])->getMock();
        $hydrator->expects($this->exactly(3))->method('addStrategy')->withConsecutive(
            ['professions', $strategy],
            ['employmentTypes', $strategy],
            ['industries', $strategy]
        );

        $this->target->setHydrator($hydrator);

        $this->target->init();
    }
}
