<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;


use CoreTestUtils\TestCase\InitValueTrait;
use Cv\Entity\Skill;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SkillTest
 * @package CvTest\Entity
 * @covers  Cv\Entity\Skill
 */
class SkillTest extends \PHPUnit_Framework_TestCase
{
    use \CoreTestUtils\TestCase\SimpleSetterAndGetterTrait, InitValueTrait;

    public function getSetterAndGetterDataProvider()
    {
        $ob = new Skill();
        return [
            [$ob, 'nativeLanguages', new ArrayCollection()],
            [$ob, 'languageSkills', new ArrayCollection()],
            [$ob, 'computerSkills', new ArrayCollection()]
        ];
    }

    public function getTestInitValue()
    {
        $ob = new Skill();
        return [
            [$ob, 'nativeLanguages', new ArrayCollection()],
            [$ob, 'languageSkills', new ArrayCollection()],
            [$ob, 'computerSKills', new ArrayCollection()],
        ];
    }


}
