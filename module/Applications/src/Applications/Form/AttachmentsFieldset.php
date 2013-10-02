<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** AttachmentsFieldset.php */ 
namespace Applications\Form;

use Zend\Form\Fieldset;

class AttachmentsFieldset extends Fieldset
{
    
     
    public function init()
    {
        $this->setName('attachment')
             ->setLabel('Attachment');
                     
        $this->add(array(
            'type' => 'file',
            'name' => 'file',
        ));
    }
}

