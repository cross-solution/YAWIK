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
     * map label of fieldset.
     */
    protected $labelMap = [
        'name'  => /*@translate*/ 'Full Name',
        'gender'  => /*@translate*/ 'Salutation',
        'company' => /*@translate*/ 'Company',
        'street' => /*@translate*/ 'Street',
        'zipCode' => /*@translate*/ 'Postalcode',
        'city' => /*@translate*/ 'City',
        'region' => /*@translate*/ 'Region',
        'country' => /*@translate*/ 'Country',
        'vatId' => /*@translate*/ 'Value added tax ID',
        'email' => /*@translate*/ 'Email address',
    ];

    public function init()
    {
        $this->setLabel(/*@translate*/ 'Invoice Address');
    }
    
}