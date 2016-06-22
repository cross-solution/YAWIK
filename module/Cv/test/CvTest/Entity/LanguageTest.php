<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Entity;

use Cv\Entity\Language;
use CoreTestUtils\TestCase\SimpleSetterAndGetterTrait;

class LanguageTestEnglish
{
    public function __toString()
    {
        return 'English';
    }
}

/**
 * Class LanguageTest
 * @covers  Cv\Entity\Language
 * @package CvTest\Entity
 */
class LanguageTest extends \PHPUnit_Framework_TestCase
{
    use SimpleSetterAndGetterTrait;

    public function getSetterAndGetterDataProvider()
    {
        $ob = new Language();
        return [
            [$ob, 'language', 'English'],
            [$ob, 'levelListening', 'Excellent'],
            [$ob, 'levelReading', 'Excellent'],
            [$ob, 'levelSpokenInteraction', 'Excellent'],
            [$ob, 'levelSpokenProduction', 'Excellent'],
            [$ob, 'levelWriting', 'Excellent'],
        ];
    }

    public function testShouldConvertLanguageToString()
    {
        $ob = new Language();
        $ob->setLanguage(new LanguageTestEnglish());
        $this->assertEquals('English', $ob->getLanguage());
    }
}
