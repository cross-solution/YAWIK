<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use PHPUnit\Framework\TestCase;

use Cv\Entity\ComputerSkill;
use CoreTestUtils\TestCase\SimpleSetterAndGetterTrait;

/**
 * Class ComputerSkillTest
 * @covers \Cv\Entity\ComputerSkill
 * @package CvTest\Entity
 */
class ComputerSkillTest extends TestCase
{
    use SimpleSetterAndGetterTrait;

    public function getSetterAndGetterDataProvider()
    {
        $ob = new ComputerSkill();
        return [
            [$ob, 'name', 'Some Name'],
            [$ob, 'level', 'Some Level'],
        ];
    }
}
