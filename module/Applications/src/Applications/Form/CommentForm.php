<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** CommentForm.php */ 
namespace Applications\Form;

use Core\Form\Form;

class CommentForm extends Form
{
    
    public function init()
    {
        $this->setName('applications-comment-form');
        
        $this->add(array(
            'name' => 'comment',
            'type' => 'Applications/CommentFieldset',
            'options' => array(
                'use_as_base_fieldset' => true,
            )
        ));
        
        $this->add(array(
            'type' => 'DefaultButtonsFieldset'
        ));
    }
}

