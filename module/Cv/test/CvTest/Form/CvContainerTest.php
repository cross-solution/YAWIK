<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CvTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Form\Container;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use Cv\Form\CvContainer;

/**
 * Tests for \Cv\Form\CvContainer
 *
 * @covers \Cv\Form\CvContainer
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group  Cv
 * @group  Cv.Form
 */
class CvContainerTest extends TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait, TestDefaultAttributesTrait;

    /**
     *
     *
     * @var array|CvContainer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        CvContainer::class,
        '@testInitializesItself' => [
            'mock' => ['setName' => ['with' => 'cv-form', 'count' => 1], 'setForms'],
        ],
        '@testSetsIdParamWhenEntityIsSet' => [
            'mock' => [ 'setParam' ],
        ],
    ];

    private $inheritance = [Container::class, ViewPartialProviderInterface::class];

    private $traits = [ViewPartialProviderTrait::class];

    private $attributes = [
        'defaultPartial' => 'cv/form/cv-container'
    ];

    public function testInitializesItself()
    {
        $formsSpec = [
            'contact'        => [
                'type'     => 'Auth/UserInfo',
                'property' => 'contact'
            ],
            'image'          => [
                'type'            => 'CvContactImage',
                'property'        => 'contact',
                'use_files_array' => true
            ],
            'preferredJob'   => [
                'type'     => 'Cv/PreferredJobForm',
                'property' => 'preferredJob',
                'options'  => [
                    'is_disable_capable'          => true,
                    'is_disable_elements_capable' => true,
                    'enable_descriptions'         => true,
                    'description'                 => 'Where do you want to work tomorrow? This heading gives an immediate overview of your desired next job.',
                ],

            ],
            'employments'    => [
                'type'     => 'CvEmploymentCollection',
                'property' => 'employmentsIndexedById'
            ],
            'educations'     => [
                'type'     => 'CvEducationCollection',
                'property' => 'educationsIndexedById'
            ],
            'nativeLanguage' => [
                'type'     => 'Cv/NativeLanguageForm',
                'property' => true,
                'options'  => [
                    'enable_descriptions' => true,
                    'description'         => 'Please select from list or enter your mother tongue.',
                ],
            ],
            'languageSkills' => [
                'type'     => 'Cv/LanguageSkillCollection',
                'property' => 'languageSkillsIndexedById',
            ],
            'skills'         => [
                'type'     => 'CvSkillCollection',
                'property' => 'skillsIndexedById'
            ],
            'attachments'    => 'Cv/Attachments'
        ];


        $this->target
            ->expects($this->once())
            ->method('setForms')
            ->with($formsSpec);

        $this->target->init();
    }

    public function testSetsIdParamWhenEntityIsSet()
    {
        $id = 'testId';

        $entity = $this
            ->getMockBuilder(AbstractIdentifiableEntity::class)
            ->setMethods(['getId'])
            ->getMockForAbstractClass();

        $entity->expects($this->once())->method('getId')->willReturn($id);

        $this->target->expects($this->once())->method('setParam')->with('cv', $id);

        $this->target->setEntity($entity);
        $this->target->setEntity($entity, 'some-key');
    }
}
