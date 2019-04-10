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

use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\EmptySummaryAwareInterface;
use Core\Form\EmptySummaryAwareTrait;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use Cv\Entity\NativeLanguage;
use Cv\Form\NativeLanguageFieldset;
use Zend\Form\Fieldset;

/**
 * Tests for \Cv\Form\NativeLanguageFieldset
 *
 * @covers \Cv\Form\NativeLanguageFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 */
class NativeLanguageFieldsetTest extends TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait, TestDefaultAttributesTrait;

    public static $languagesOptions = [
        'ab' =>  'Abkhazian',
        'af' =>  'Afrikaans',
        'sq' =>  'Albanian',
        'am' =>  'Amharic',
        'ar' =>  'Arabic',
        'hy' =>  'Armenian',
        'as' =>  'Assamese',
        'az' =>  'Azerbaijani',
        'eu' =>  'Basque',
        'be' =>  'Belarusian',
        'bn' =>  'Bengali',
        'bs' =>  'Bosnian',
        'br' =>  'Breton',
        'bg' =>  'Bulgarian',
        'my' =>  'Burmese',
        'ca' =>  'Catalan/Valencian',
        'ce' =>  'Chechen',
        'zh' =>  'Chinese',
        'kw' =>  'Cornish',
        'co' =>  'Corsican',
        'hr' =>  'Croatian',
        'cs' =>  'Czech',
        'da' =>  'Danish',
        'nl' =>  'Dutch',
        'en' =>  'English',
        'et' =>  'Estonian',
        'fo' =>  'Faroese',
        'fj' =>  'Fijian',
        'fi' =>  'Finnish',
        'fr' =>  'French',
        'gd' =>  'Gaelic/Scottish Gaelic',
        'gl' =>  'Galician',
        'ka' =>  'Georgian',
        'de' =>  'German',
        'el' =>  'Greek',
        'gu' =>  'Gujarati',
        'ht' =>  'Haitian/Haitian Creole',
        'he' =>  'Hebrew',
        'hi' =>  'Hindi',
        'hu' =>  'Hungarian',
        'is' =>  'Icelandic',
        'id' =>  'Indonesian',
        'ga' =>  'Irish',
        'it' =>  'Italian',
        'ja' =>  'Japanese',
        'jv' =>  'Javanese',
        'kn' =>  'Kannada',
        'kk' =>  'Kazakh',
        'ky' =>  'Kirghiz',
        'kg' =>  'Kongo/Kikongo',
        'ko' =>  'Korean',
        'ku' =>  'Kurdish',
        'lo' =>  'Lao',
        'lv' =>  'Latvian',
        'li' =>  'Limburgish/Limburgian/Limburgic',
        'ln' =>  'Lingala',
        'lt' =>  'Lithuanian',
        'lb' =>  'Luxembourgish',
        'mk' =>  'Macedonian',
        'mg' =>  'Malagasy',
        'ms' =>  'Malay',
        'ml' =>  'Malayalam',
        'mt' =>  'Maltese',
        'gv' =>  'Manx',
        'mr' =>  'Marathi',
        'mo' =>  'Moldavian',
        'mn' =>  'Mongolian',
        'ne' =>  'Nepali',
        'no' =>  'Norwegian',
        'nb' =>  'Norwegian (Bokmal)',
        'nn' =>  'Norwegian (Nynorsk)',
        'ps' =>  'Pashto',
        'fa' =>  'Persian',
        'pl' =>  'Polish',
        'pt' =>  'Portuguese',
        'pa' =>  'Punjabi',
        'rm' =>  'Romani',
        'ro' =>  'Romanian',
        'ru' =>  'Russian',
        'sc' =>  'Sardinian',
        'sr' =>  'Serbian',
        'sd' =>  'Sindhi',
        'sk' =>  'Slovak',
        'sl' =>  'Slovenian',
        'so' =>  'Somali',
        'es' =>  'Spanish',
        'sw' =>  'Swahili',
        'sv' =>  'Swedish',
        'tl' =>  'Tagalog',
        'ty' =>  'Tahitian',
        'tg' =>  'Tajik',
        'ta' =>  'Tamil',
        'tt' =>  'Tatar',
        'te' =>  'Telugu',
        'th' =>  'Thai',
        'bo' =>  'Tibetan',
        'tr' =>  'Turkish',
        'tk' =>  'Turkmen',
        'uk' =>  'Ukrainian',
        'ur' =>  'Urdu',
        'uz' =>  'Uzbek',
        'vi' =>  'Vietnamese',
        'cy' =>  'Welsh',
        'fy' =>  'Western Frisian',
        'yi' =>  'Yiddish',
        'yo' =>  'Yoruba'
    ];
    
    /**
     *
     *
     * @var array|NativeLanguageFieldset|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        NativeLanguageFieldset::class,
        '@testInitializesItself' => [
            'mock' => [
                'add',
                'setName' => ['with' => 'nativeLanguages', 'count' => 1, 'return' => '__self__'],
                'setHydrator' => ['@with' => ['isInstanceOf', EntityHydrator::class ], 'count' => 1, 'return' => '__self__'],
                'setObject' => ['@with' => ['isInstanceOf', NativeLanguage::class ], 'count' => 1, 'return' => '__self__'],
                'setLabel' => ['with' => 'Native Language', 'count' => 1]
            ],
            'args' => false,
        ],
    ];

    private $inheritance = [ Fieldset::class, EmptySummaryAwareInterface::class ];

    private $traits = [ EmptySummaryAwareTrait::class ];

    private function getDefaultAttributes()
    {
        return [
            'languagesOptions' => self::$languagesOptions,
            'defaultEmptySummaryNotice' => 'Click here to enter your native language(s)',
        ];
    }

    public function testInitializesItself()
    {
        $add = [
                    'name'       => 'nativeLanguages',
                    'type'       => 'Core\Form\Element\Select',
                    'options'    => [
                        'label'         => 'Language',
                        'value_options' => self::$languagesOptions
                    ],
                    'attributes' => [
                        'id'       => 'languageskill-language',
                        'title'    => /*@translate */ 'what is your native language',
                        'multiple' => true,
                        'data-allowclear' => true,
                        'data-searchbox' => -1,
                    ]
            ];

        $this->target
            ->expects($this->once())
            ->method('add')
            ->with($add)
        ;

        $this->target->init();
    }
}
