<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use PHPUnit\Framework\TestCase;


use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Entity\Skill;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SkillTest
 *
 * @covers \Cv\Entity\Skill
 * @group Cv
 * @group Cv.Entity
 */
class SkillTest extends TestCase
{
    #use \CoreTestUtils\TestCase\SimpleSetterAndGetterTrait, InitValueTrait;
    use TestInheritanceTrait, TestSetterGetterTrait;

    protected $target = Skill::class;

    protected $inheritance = [ '\Core\Entity\AbstractIdentifiableEntity' ];

    public function propertiesProvider()
    {
        $options = [
            'default' => '@' . ArrayCollection::class,
            'value' => new ArrayCollection(),
        ];

        return [
            [ 'nativeLanguages', $options ],
            [ 'languageSkills',  $options ],
            [ 'computerSkills',  $options ],
        ];
    }
}
