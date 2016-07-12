<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use CoreTestUtils\TestCase\SimpleSetterAndGetterTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Entity\NativeLanguage;

class NativeLanguageTestEnglish
{
    public function __toString()
    {
        return 'English';
    }
}

/**
 * Class NativeLanguageTest
 * @covers  Cv\Entity\NativeLanguage
 * @package CvTest\Entity
 */
class NativeLanguageTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = NativeLanguage::class;

    private $inheritance = [ 'Core\Entity\AbstractEntity' ];

    private $properties = [
        [ 'language', 'Some Language' ],
        [ 'language', [ '@value' => NativeLanguageTestEnglish::class, 'expect' => 'English' ]]
    ];
}
