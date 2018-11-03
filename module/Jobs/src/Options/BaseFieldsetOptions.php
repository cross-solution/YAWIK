<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
/**
 * to configure the options, please copy the config/JobboardSearchOptions.config.local.php.dist into your
 * config/autoload directory.
 */
  
/** */
namespace Jobs\Options;

use Core\Options\FieldsetCustomizationOptions;

/**
 * ${CARET}
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class BaseFieldsetOptions extends FieldsetCustomizationOptions
{
    const TITLE =  'title';
    const LOCATION = 'geoLocation';

    /**
     * Fields can be disabled.
     *
     * @var array
     */
    protected $fields=[
        self::TITLE => [
            'enabled' => true,
            'options' => [
                    'label' => /*@translate*/ 'Job title',
                    'description' => /*@translate*/ 'Please enter the job title'
            ],
        ],
        self::LOCATION => [
            /*
             * If you need to configure a fix list of location, you can use the SimpleLocationSelect Element
             */
            //'type' => 'SimpleLocationSelect',
            'enabled' => true,
            'options' => [
                'label' => /*@translate*/ 'Location',
                'description' => /*@translate*/ 'Please enter the location of the job',
                /*
                * If you use the SimpleLocationSelect Element, you can set the values here. Keys are containing a
                * serialized Job\Entity\Location entity.
                */
//              'value_options' => [
//                  '{"country":"Deutschland","postalcode":"70173","city":"Stuttgart","region":"Baden-Württemberg","coordinates":{"type":"Point","coordinates":[9.17702,48.78232]}}' => 'Stuttgart',
//                  '{"country":"Deutschland","postalcode":"80331","city":"München","region":"Bayern","coordinates":{"type":"Point","coordinates":[11.57549,48.13743]}}' => 'München',
//                  '{"country":"Deutschland","postalcode":"60486","city":"Frankfurt","region":"Hessen","coordinates":{"type":"Point","coordinates":[8.68212,50.11092]}}' => 'Frankfurt',
//            ],
            ]
//            'attributes' => [
//              'class' => '',
//              'data-searchbox' => '-1',  // disable search, in case using the "SimpleLocationSelect"
//              'data-placeholder' => /*@translate*/ 'please select',
//              'data-allowclear' => 'false',
//              'data-width' => '100%'
//            ]
        ],
    ];
}
