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

use Core\Form\Hydrator\Strategy\CollectionStrategy;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Entity\Skill;
use Cv\Form\SkillFieldset;

/**
 * Tests for \Cv\Form\SkillFieldset
 *
 * @covers \Cv\Form\SkillFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 */
class SkillFieldsetTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     * @var array|SkillFieldset|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        SkillFieldset::class,
        '@testInitializesItself' => [
            'mock' => [
                'setName' => ['with' => 'skill', 'count' => 1, 'return' => '__self__'],
                'setLabel' => ['with' => 'Languages', 'count' => 1, 'return' => '__self__'],
                'setObject' => [ '@with' => ['isInstanceOf', Skill::class], 'count' => 1],
                'add'
            ],
        ],
    ];

    private $inheritance = ['Zend\Form\Fieldset'];

    public function testGetHydratorAttachsStrategyToCreatedInstance()
    {
        /* @var \Core\Entity\Hydrator\EntityHydrator $hydrator */
        $hydrator = $this->target->getHydrator();

        $this->assertInstanceOf('Core\Entity\Hydrator\EntityHydrator', $hydrator);
        $this->assertTrue($hydrator->hasStrategy('nativeLanguages'));
        $this->assertInstanceOf(CollectionStrategy::class, $hydrator->getStrategy('nativeLanguages'));
        $this->assertTrue($hydrator->hasStrategy('languageSkills'));
        $this->assertInstanceOf(CollectionStrategy::class, $hydrator->getStrategy('languageSkills'));
    }
    
    public function testInitializesItself()
    {
        $this->target
            ->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [[
                    'type' => 'Collection',
                    'name' => 'nativeLanguages',
                    'options' => [
                        'label' =>  'Native Language',
                        'count' => 1,
                        'should_create_template' => true,
                        'allow_add' => true,
                        'target_element' => [
                            'type' => 'Cv/NativeLanguageFieldset'
                        ]
                    ]
                ]],
                [[
                    'type' => 'Collection',
                    'name' => 'languageSkills',
                    'options' => [
                        'label' =>  'Other languages',
                        'count' => 1,
                        'should_create_template' => true,
                        'allow_add' => true,
                        'target_element' => [
                            'type' => 'LanguageSkillFieldset'
                        ]
                    ]
                ]]
            )
        ;

        $this->target->init();
    }
}
