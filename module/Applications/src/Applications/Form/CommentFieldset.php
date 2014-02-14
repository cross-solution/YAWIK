<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** CommentFieldset.php */ 
namespace Applications\Form;

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

class CommentFieldset extends Fieldset
{
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new EntityHydrator());
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $this->setName('comment');
        
        $this->add(array(
            'type' => 'Core/RatingFieldset',
            'name' => 'rating',
        ));
        
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'message',
            'options' => array(
                'label' => /* @translate */ 'Comment message',
            )
        ));
        
    }
}

