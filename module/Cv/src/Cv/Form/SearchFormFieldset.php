<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Cv\Form;

use Core\Form\TextSearchFormFieldset;

class SearchFormFieldset extends TextSearchFormFieldset
{

    protected $locationEngineType = 'photon';

    public function init()
    {
        $this->addTextElement(
            'Search',
            /*@translate*/
            'Search for Applicant Name'
        );

        $this->add(
            array(
                'name' => 'l',
                'type' => 'Location',
                'options' => array(
                    'label' => /*@translate*/
                        'Location',
                    'engine_type' => $this->locationEngineType,
                ),
            )
        );

        $this->add(
            array(
                'name' => 'd',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => /*@translate*/
                        'Distance',
                    'value_options' => [
                        '5' => '5 km',
                        '10' => '10 km',
                        '20' => '20 km',
                        '50' => '50 km',
                        '100' => '100 km'
                    ],

                ),
                'attributes' => [
                    'value' => '10', // default distance
                    'data-searchbox' => -1,  // hide the search box
                    'data-allowclear' => 'false', // allow to clear a selected value
                    'data-placeholder' => /*@translate*/
                        'Distance',
                ]
            )
        );
    }
}