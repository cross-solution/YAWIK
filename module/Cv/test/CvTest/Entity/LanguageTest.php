<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\AbstractIdentifiableEntity;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Entity\Language;
use Cv\Entity\LanguageInterface;

class LanguageTestEnglish
{
    public function __toString()
    {
        return 'English';
    }
}

/**
 * Class LanguageTest
 * @covers \Cv\Entity\Language
 * @group Cv
 * @group Cv.Entity
 */
class LanguageTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = Language::class;

    private $inheritance = [ AbstractIdentifiableEntity::class , LanguageInterface::class ];

    private $properties = [
        ['language', 'English'],
        ['language', ['@value' => LanguageTestEnglish::class, 'expect' => 'English']],
        ['levelListening', 'Excellent'],
        ['levelReading', 'Excellent'],
        ['levelSpokenInteraction', 'Excellent'],
        ['levelSpokenProduction', 'Excellent'],
        ['levelWriting', 'Excellent'],
    ];
}
