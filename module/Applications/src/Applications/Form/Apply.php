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

use Core\Form\Container;
use Core\Entity\EntityInterface;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */

class Apply extends Container
{
    
    public function init()
    {
        $this->setForms(array(
            'contact' => 'Applications/Contact',
            'base'    => array(
                'type' => 'Applications/Base',
                'property' => true,
            ),
        ));
    }
    
}
