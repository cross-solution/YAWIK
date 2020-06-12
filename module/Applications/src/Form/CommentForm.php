<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** CommentFieldset.php */
namespace Applications\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\Form;

class CommentForm extends Form
{
    /**
     * Gets the hydrator
     *
     * @return \Laminas\Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new EntityHydrator());
        }
        return $this->hydrator;
    }

    /**
     * initialize comments form
     */
    public function init()
    {
        $this->setName('application-comment-form');

        $this->add(
            array(
            'type' => 'Core/RatingFieldset',
            'name' => 'rating',
            )
        );

        $this->add(
            array(
            'type' => 'Textarea',
            'name' => 'message',
            'options' => array(
                'label' => /* @translate */ 'Comment message',
            )
            )
        );
    }
}
