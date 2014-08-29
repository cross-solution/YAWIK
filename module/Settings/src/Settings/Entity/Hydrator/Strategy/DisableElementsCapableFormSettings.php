<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright 2013-2014 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Settings\Entity\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

/**
 * Strategy to hydrate / extract disable elements configuration to / from an settings container.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de> 
 */
class DisableElementsCapableFormSettings implements StrategyInterface
{

    public function extract($value, $object = null) {
         return $value;
    }

    public function hydrate($value, array $data = null) {
        /*
         * We needed to serialize the value array in the form
         * (due to {@link \Zend\Form\Form:prepareBindValues()}),
         * so we must unserialize it here to hydrate the container with
         * an array.
         */
        return unserialize($value);
    }
}