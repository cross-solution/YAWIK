<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use CoreTestUtils\TestCase\SimpleSetterAndGetterTrait;
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
    use SimpleSetterAndGetterTrait;

    public function getSetterAndGetterDataProvider()
    {
        $ob = new NativeLanguage();
        return [
            [$ob, 'language', 'Some Language'],
        ];
    }

    public function testShouldConvertLanguageToString()
    {
        $ob = new NativeLanguage();
        $ob->setLanguage(new NativeLanguageTestEnglish());
        $this->assertEquals('English', $ob->getLanguage());
    }
}
