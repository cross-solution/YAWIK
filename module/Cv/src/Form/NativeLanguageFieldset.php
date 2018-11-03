<?php

namespace Cv\Form;

use Zend\Form\Fieldset;
use Cv\Entity\NativeLanguage as NativeLanguageEntity;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\EmptySummaryAwareInterface;
use Core\Form\EmptySummaryAwareTrait;

class NativeLanguageFieldset extends Fieldset implements EmptySummaryAwareInterface
{
    use EmptySummaryAwareTrait;

    private $defaultEmptySummaryNotice = /*@translate*/ 'Click here to enter your native language(s)';
    
    /**
     * languages iso 639-1
     * @var array
     */
    public static $languagesOptions = [
        'ab' => /*@translate*/ 'Abkhazian',
        'af' => /*@translate*/ 'Afrikaans',
        'sq' => /*@translate*/ 'Albanian',
        'am' => /*@translate*/ 'Amharic',
        'ar' => /*@translate*/ 'Arabic',
        'hy' => /*@translate*/ 'Armenian',
        'as' => /*@translate*/ 'Assamese',
        'az' => /*@translate*/ 'Azerbaijani',
        'eu' => /*@translate*/ 'Basque',
        'be' => /*@translate*/ 'Belarusian',
        'bn' => /*@translate*/ 'Bengali',
        'bs' => /*@translate*/ 'Bosnian',
        'br' => /*@translate*/ 'Breton',
        'bg' => /*@translate*/ 'Bulgarian',
        'my' => /*@translate*/ 'Burmese',
        'ca' => /*@translate*/ 'Catalan/Valencian',
        'ce' => /*@translate*/ 'Chechen',
        'zh' => /*@translate*/ 'Chinese',
        'kw' => /*@translate*/ 'Cornish',
        'co' => /*@translate*/ 'Corsican',
        'hr' => /*@translate*/ 'Croatian',
        'cs' => /*@translate*/ 'Czech',
        'da' => /*@translate*/ 'Danish',
        'nl' => /*@translate*/ 'Dutch',
        'en' => /*@translate*/ 'English',
        'et' => /*@translate*/ 'Estonian',
        'fo' => /*@translate*/ 'Faroese',
        'fj' => /*@translate*/ 'Fijian',
        'fi' => /*@translate*/ 'Finnish',
        'fr' => /*@translate*/ 'French',
        'gd' => /*@translate*/ 'Gaelic/Scottish Gaelic',
        'gl' => /*@translate*/ 'Galician',
        'ka' => /*@translate*/ 'Georgian',
        'de' => /*@translate*/ 'German',
        'el' => /*@translate*/ 'Greek',
        'gu' => /*@translate*/ 'Gujarati',
        'ht' => /*@translate*/ 'Haitian/Haitian Creole',
        'he' => /*@translate*/ 'Hebrew',
        'hi' => /*@translate*/ 'Hindi',
        'hu' => /*@translate*/ 'Hungarian',
        'is' => /*@translate*/ 'Icelandic',
        'id' => /*@translate*/ 'Indonesian',
        'ga' => /*@translate*/ 'Irish',
        'it' => /*@translate*/ 'Italian',
        'ja' => /*@translate*/ 'Japanese',
        'jv' => /*@translate*/ 'Javanese',
        'kn' => /*@translate*/ 'Kannada',
        'kk' => /*@translate*/ 'Kazakh',
        'ky' => /*@translate*/ 'Kirghiz',
        'kg' => /*@translate*/ 'Kongo/Kikongo',
        'ko' => /*@translate*/ 'Korean',
        'ku' => /*@translate*/ 'Kurdish',
        'lo' => /*@translate*/ 'Lao',
        'lv' => /*@translate*/ 'Latvian',
        'li' => /*@translate*/ 'Limburgish/Limburgian/Limburgic',
        'ln' => /*@translate*/ 'Lingala',
        'lt' => /*@translate*/ 'Lithuanian',
        'lb' => /*@translate*/ 'Luxembourgish',
        'mk' => /*@translate*/ 'Macedonian',
        'mg' => /*@translate*/ 'Malagasy',
        'ms' => /*@translate*/ 'Malay',
        'ml' => /*@translate*/ 'Malayalam',
        'mt' => /*@translate*/ 'Maltese',
        'gv' => /*@translate*/ 'Manx',
        'mr' => /*@translate*/ 'Marathi',
        'mo' => /*@translate*/ 'Moldavian', // [mo] for Moldavian has been withdrawn
        'mn' => /*@translate*/ 'Mongolian',
        'ne' => /*@translate*/ 'Nepali',
        'no' => /*@translate*/ 'Norwegian',
        'nb' => /*@translate*/ 'Norwegian (Bokmal)',
        'nn' => /*@translate*/ 'Norwegian (Nynorsk)',
        'ps' => /*@translate*/ 'Pashto',
        'fa' => /*@translate*/ 'Persian',
        'pl' => /*@translate*/ 'Polish',
        'pt' => /*@translate*/ 'Portuguese',
        'pa' => /*@translate*/ 'Punjabi',
        'rm' => /*@translate*/ 'Romani',
        'ro' => /*@translate*/ 'Romanian',
        'ru' => /*@translate*/ 'Russian',
        'sc' => /*@translate*/ 'Sardinian',
        'sr' => /*@translate*/ 'Serbian',
        'sd' => /*@translate*/ 'Sindhi',
        'sk' => /*@translate*/ 'Slovak',
        'sl' => /*@translate*/ 'Slovenian',
        'so' => /*@translate*/ 'Somali',
        'es' => /*@translate*/ 'Spanish',
        'sw' => /*@translate*/ 'Swahili',
        'sv' => /*@translate*/ 'Swedish',
        'tl' => /*@translate*/ 'Tagalog',
        'ty' => /*@translate*/ 'Tahitian',
        'tg' => /*@translate*/ 'Tajik',
        'ta' => /*@translate*/ 'Tamil',
        'tt' => /*@translate*/ 'Tatar',
        'te' => /*@translate*/ 'Telugu',
        'th' => /*@translate*/ 'Thai',
        'bo' => /*@translate*/ 'Tibetan',
        'tr' => /*@translate*/ 'Turkish',
        'tk' => /*@translate*/ 'Turkmen',
        'uk' => /*@translate*/ 'Ukrainian',
        'ur' => /*@translate*/ 'Urdu',
        'uz' => /*@translate*/ 'Uzbek',
        'vi' => /*@translate*/ 'Vietnamese',
        'cy' => /*@translate*/ 'Welsh',
        'fy' => /*@translate*/ 'Western Frisian',
        'yi' => /*@translate*/ 'Yiddish',
        'yo' => /*@translate*/ 'Yoruba'
    ];

    public function init()
    {
        $this->setName('nativeLanguages')
             ->setHydrator(new EntityHydrator())
             ->setObject(new NativeLanguageEntity())
             ->setLabel('Native Language');


        $this->add(
            [
                'name'       => 'nativeLanguages',
                'type'       => 'Core\Form\Element\Select',
                'options'    => array(
                    'label'         => 'Language',
                    'value_options' => self::$languagesOptions
                ),
                'attributes' => [
                    'id'       => 'languageskill-language',
                    'title'    => /*@translate */ 'what is your native language',
                    'multiple' => true,
                    'data-allowclear' => true,
                    'data-searchbox' => -1,
                ]
            ]
        );
    }
}
