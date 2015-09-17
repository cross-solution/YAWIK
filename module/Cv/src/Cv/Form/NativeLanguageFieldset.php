<?php

namespace Cv\Form;

use Zend\Form\Fieldset;
use Cv\Entity\NativeLanguage as NativeLanguageEntity;
use Core\Entity\Hydrator\EntityHydrator;

class NativeLanguageFieldset extends Fieldset
{
    public function init()
    {
        $this->setName('nativeLanguage')
            ->setHydrator(new EntityHydrator())
            ->setObject(new NativeLanguageEntity())
            ->setLabel('Native Language');
        
        
        $this->add(
            array(
            'name' => 'language',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Language',
                'value_options' => array(
            /*@translate*/ 'Abkhazian',
            /*@translate*/ 'Afrikaans',
            /*@translate*/ 'Albanian',
            /*@translate*/ 'Amharic',
            /*@translate*/ 'Arabic',
            /*@translate*/ 'Armenian',
            /*@translate*/ 'Assamese',
            /*@translate*/ 'Azerbaijani',
            /*@translate*/ 'Basque',
            /*@translate*/ 'Belarusian',
            /*@translate*/ 'Bengali',
            /*@translate*/ 'Bosnian',
            /*@translate*/ 'Breton',
            /*@translate*/ 'Bulgarian',
            /*@translate*/ 'Burmese',
            /*@translate*/ 'Catalan/Valencian',
            /*@translate*/ 'Chechen',
            /*@translate*/ 'Chinese',
            /*@translate*/ 'Cornish',
            /*@translate*/ 'Corsican',
            /*@translate*/ 'Croatian',
            /*@translate*/ 'Czech',
            /*@translate*/ 'Danish',
            /*@translate*/ 'Dutch',
            /*@translate*/ 'Estonian',
            /*@translate*/ 'Faroese',
            /*@translate*/ 'Fijian',
            /*@translate*/ 'Finnish',
            /*@translate*/ 'French',
            /*@translate*/ 'Gaelic/Scottish Gaelic',
            /*@translate*/ 'Galician',
            /*@translate*/ 'Georgian',
            /*@translate*/ 'German',
            /*@translate*/ 'Greek',
            /*@translate*/ 'Gujarati',
            /*@translate*/ 'Haitian/Haitian Creole',
            /*@translate*/ 'Hebrew',
            /*@translate*/ 'Hindi',
            /*@translate*/ 'Hungarian',
            /*@translate*/ 'Icelandic',
            /*@translate*/ 'Indonesian',
            /*@translate*/ 'Irish',
            /*@translate*/ 'Italian',
            /*@translate*/ 'Japanese',
            /*@translate*/ 'Javanese',
            /*@translate*/ 'Kannada',
            /*@translate*/ 'Kazakh',
            /*@translate*/ 'Kirghiz',
            /*@translate*/ 'Kongo/Kikongo',
            /*@translate*/ 'Korean',
            /*@translate*/ 'Kurdish',
            /*@translate*/ 'Lao',
            /*@translate*/ 'Latvian',
            /*@translate*/ 'Limburgish/Limburgian/Limburgic',
            /*@translate*/ 'Lingala',
            /*@translate*/ 'Lithuanian',
            /*@translate*/ 'Luxembourgish',
            /*@translate*/ 'Macedonian',
            /*@translate*/ 'Malagasy',
            /*@translate*/ 'Malay',
            /*@translate*/ 'Malayalam',
            /*@translate*/ 'Maltese',
            /*@translate*/ 'Manx',
            /*@translate*/ 'Marathi',
            /*@translate*/ 'Moldavian',
            /*@translate*/ 'Mongolian',
            /*@translate*/ 'Nepali',
            /*@translate*/ 'Norwegian',
            /*@translate*/ 'Norwegian (Bokmal)',
            /*@translate*/ 'Norwegian (Nynorsk)',
            /*@translate*/ 'Pashto',
            /*@translate*/ 'Persian',
            /*@translate*/ 'Polish',
            /*@translate*/ 'Portuguese',
            /*@translate*/ 'Punjabi',
            /*@translate*/ 'Raeto-Romance',
            /*@translate*/ 'Romani',
            /*@translate*/ 'Romanian',
            /*@translate*/ 'Russian',
            /*@translate*/ 'Sami',
            /*@translate*/ 'Sardinian',
            /*@translate*/ 'Serbian',
            /*@translate*/ 'Sindhi',
            /*@translate*/ 'Slovak',
            /*@translate*/ 'Slovenian',
            /*@translate*/ 'Somali',
            /*@translate*/ 'Spanish',
            /*@translate*/ 'Swahili',
            /*@translate*/ 'Swedish',
            /*@translate*/ 'Tagalog',
            /*@translate*/ 'Tahitian',
            /*@translate*/ 'Tajik',
            /*@translate*/ 'Tamil',
            /*@translate*/ 'Tatar',
            /*@translate*/ 'Telugu',
            /*@translate*/ 'Thai',
            /*@translate*/ 'Tibetan',
            /*@translate*/ 'Turkish',
            /*@translate*/ 'Turkmen',
            /*@translate*/ 'Ukrainian',
            /*@translate*/ 'Urdu',
            /*@translate*/ 'Uzbek',
            /*@translate*/ 'Vietnamese',
            /*@translate*/ 'Welsh',
            /*@translate*/ 'Western Frisian',
            /*@translate*/ 'Yiddish',
            /*@translate*/ 'Yoruba'                        )
            ),
            'attributes' => array(
                        'id' => 'languageskill-language',
                        'title' => /*@translate */ 'what is your native language'
            )
            )
        );
        
               
    }
}
