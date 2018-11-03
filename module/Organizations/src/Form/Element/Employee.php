<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Form\Element;

use Auth\Entity\UserInterface;
use Core\Form\ViewPartialProviderInterface;
use Zend\Form\Element;

/**
 * An employee form element.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.18
 */
class Employee extends Element
{

    /**
     * Gets the user id or a placeholder for use in collection template.
     *
     * @return string
     */
    public function getValue()
    {
        $value = parent::getValue();

        return $value instanceof UserInterface ? $value->getId() : '__userId__';
    }

    /**
     * Gets the user entity or null.
     *
     * @return UserInterface|null
     */
    public function getUser()
    {
        $value = parent::getValue();

        return $value instanceof UserInterface ? $value : null;
    }
}
