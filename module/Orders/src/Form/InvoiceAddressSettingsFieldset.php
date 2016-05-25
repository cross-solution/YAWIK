<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Form;

use Settings\Form\SettingsFieldset;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class InvoiceAddressSettingsFieldset extends SettingsFieldset
{
    /*
     * map label of fieldset. You can set the order by using an array [<label>,<priority>]
     */
    protected $labelMap = [
        'name'  => [ /*@translate*/ 'Contact Person', -20],
        'company' => /*@translate*/ 'Company',
        'street' => /*@translate*/ 'Street',
        'zipCode' => /*@translate*/ 'Postalcode',
        'city' => /*@translate*/ 'City',
        'region' => /*@translate*/ 'Region',
        'country' => /*@translate*/ 'Country',
        'vatId' => /*@translate*/ 'Value added tax ID',
        'email' =>  [/*@translate*/ 'Email Address', -30]
    ];

    public function init()
    {
        $this->setLabel(/*@translate*/ 'Invoice Address');

        $this->add(
            [
                'name'       => 'gender',
                'type'       => 'Zend\Form\Element\Select',
                'options'    => [
                    'label'         => /*@translate */ 'Salutation',
                    'value_options' => [
                        ''       => '',
                        'male'   => /*@translate */ 'Mr.',
                        'female' => /*@translate */ 'Mrs.',
                    ]
                ],
                'attributes' => [
                    'data-placeholder' => /*@translate*/ 'please select',
                    'data-allowclear' => 'false',
                    'data-searchbox' => -1,  // hide the search box
                    'required' => true, // mark label as required
                ],
            ],
            ['priority'=>-10]
        );


    }

    
}