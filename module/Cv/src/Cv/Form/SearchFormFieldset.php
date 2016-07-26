<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Cv\Form;

use Zend\Form\Fieldset;

class SearchFormFieldset extends Fieldset
{

    protected $locationEngineType;

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
        if (array_key_exists('location_engine_type', $options)) {
            $this->locationEngineType = $options['location_engine_type'];
        }
    }

    public function init()
    {
        $this->setName('params');
        $this->add([
            'name' => 'search',
            'options' => [
                'label' => /*@translate*/ 'Search for resumes'
            ]
        ]);

        $this->add(
            [
                'name' => 'l',
                'type' => 'Location',
                'options' => [
                    'label' => /*@translate*/ 'Location',
                    'engine_type' => $this->locationEngineType,
                ],
            ]
        );

        $this->add(
            [
                'name' => 'd',
                'type' => 'Zend\Form\Element\Select',
                'options' => [
                    'label' => /*@translate*/ 'Distance',
                    'value_options' => [
                        '5' => '5 km',
                        '10' => '10 km',
                        '20' => '20 km',
                        '50' => '50 km',
                        '100' => '100 km'
                    ],

                ],
                'attributes' => [
                    'value' => '10', // default distance
                    'data-searchbox' => -1,  // hide the search box
                    'data-allowclear' => 'false', // allow to clear a selected value
                    'data-placeholder' => /*@translate*/ 'Distance',
                ]
            ]
        );
    }
}