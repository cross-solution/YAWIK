<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Applications\Form;

use Core\Form\Form;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class Attributes extends Form
{
    public function init()
    {
        $this->setLabel(/*@translate*/ 'Attributes')
             ->setAttribute('data-submit-on', 'checkbox');
        
        
        $this->add(array(
            'type' => 'checkbox',
            'name' => 'sendCarbonCopy',
            'options' => array(
                'label' => /*@translate*/ 'Carbon Copy',
                'description' => /*@translate*/ 'send me a carbon copy of my application'
            ),
            'attributes' => array(
                'data-validate' => 'sendCarbonCopy',
                'data-trigger'  => 'submit',
            ),
        ));
        
        $this->add(array(
            'type' => 'checkbox',
            'name' => 'acceptedPrivacyPolice',
            'options' => array(
                'label' => /*@translate*/ 'Privacy Police',
                'description' => /*@translate*/ 'I have read the privacy police and accept it'
            ),
            'attributes' => array(
                'data-validate' => 'acceptedPrivacyPolice',
                'data-trigger' => 'submit',
            ),
        ));
    }
}
