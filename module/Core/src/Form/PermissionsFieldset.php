<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PermissionsFieldset.php */
namespace Core\Form;

use Zend\Form\Fieldset;
use Auth\Entity\UserInterface;

class PermissionsFieldset extends Fieldset implements ViewPartialProviderInterface
{
    protected $partial = 'core/form/permissions-fieldset';
    
    public function setViewPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }
    
    public function getViewPartial()
    {
        return $this->partial;
    }
    
    public function init()
    {
        $this->setName('permission');
        
        $this->add(
            array(
            'type' => 'Hidden',
            'name' => 'type',
            'attributes' => array(
                'value' => '__template__',
            ),
            )
        );
        
        $this->add(
            array(
            'type' => 'Hidden',
            'name' => 'id',
            'attributes' => array(
                'value' => '__id__',
            ),
            )
        );
        
//         $this->add(array(
//             'type' => 'PermissionsGroupSelect',
//         ));
        
//         $this->add(array(
//             'type' => 'PermissionsUserInput',
//             'name' => 'user',
//         ));
        
        $this->add(
            array(
            'type' => 'MultiCheckbox',
            'name' => 'rights',
            'options' => array(
                'label' => /*@translate*/ 'Rights',
                'value_options' => array(
                    'view' => /*@translate*/ 'View',
                    'write' => /*@translate*/ 'Write',
                ),
            ),
            )
        );
    }
}
