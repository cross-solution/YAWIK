<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AttachmentsFieldset.php */ 
namespace Applications\Form;

use Zend\Form\Fieldset;

/**
 * Fieldset for base informations of an application.
 * 
 * Currently, this is only the freetext summary.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class BaseFieldset extends Fieldset
{
    
    
    /**
     * {@inheritDoc}
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        $this->setName('base')
             //->setLabel('Summary')
             ->setHydrator(new \Core\Entity\Hydrator\EntityHydrator());
             
                     
        $this->add(array(
            'type' => 'textarea',
            'name' => 'summary',
            'options' => array(
                'description' => '<strong>Please note</strong>: It is not allowed to use HTML tags. Line breaks are preserved.'
                //'label' => /*@translate*/ 'Summary'
            ),
        ));
    }
}

