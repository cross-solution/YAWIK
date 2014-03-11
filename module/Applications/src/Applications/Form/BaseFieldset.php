<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** AttachmentsFieldset.php */ 
namespace Applications\Form;

use Zend\Form\Fieldset;

class BaseFieldset extends Fieldset
{
    
     
    public function init()
    {
        $this->setName('base')
             ->setLabel('Summary')
             ->setHydrator(new \Core\Entity\Hydrator\EntityHydrator());
             
                     
        $this->add(array(
            'type' => 'textarea',
            'name' => 'summary',
            'options' => array(
                //'label' => /*@translate*/ 'Summary'
            ),
        ));
    }
}

