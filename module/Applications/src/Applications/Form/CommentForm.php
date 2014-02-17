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


use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\Form;

class CommentForm extends Form
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
        $this->setName('application-comment-form');
        
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

