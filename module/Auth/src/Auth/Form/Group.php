<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Group.php */ 
namespace Auth\Form;

use Core\Form\Form;
use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

class Group extends Form 
{
    
    public function init()
    {

        $this->add(array(
            'type' => 'Auth/Group/Data',
            'options' => array(
                'mode' => $this->getOption('mode')
            ),
        ));

        $this->add(array(
            'type' => 'DefaultButtonsFieldset',
        ));
    }
    
  
}

