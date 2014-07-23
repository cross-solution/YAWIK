<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Applications forms */ 
namespace Applications\Form;

use Core\Form\Container;

/**
 * Application forms container 
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Apply extends Container
{
    
    /**
     * {@inheritDoc}
     * 
     * Adds the standard forms and child containers.
     * 
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        $this->setForms(array(
            'contact' => 'Applications/Contact',
            'base'    => array(
                'type' => 'Applications/Base',
                'property' => true,
            ),
            'profiles' => 'Auth/SocialProfiles',
            'attachments' => 'Applications/Attachments',
            'attributes' => 'Applications/Attributes',
        ));
    }
    
}
