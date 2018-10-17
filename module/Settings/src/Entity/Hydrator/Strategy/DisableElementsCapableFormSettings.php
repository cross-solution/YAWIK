<?php
/**
 * YAWIK
 *
 * @filesource
 * @license   MIT
 * @copyright 2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Settings\Entity\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Strategy to hydrate / extract disable elements configuration to / from an settings container.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class DisableElementsCapableFormSettings implements StrategyInterface
{
    public function extract($value)
    {
        $value = $this->filterArrayStrings($value, '#dot#', '.');

        return $value;
    }

    public function hydrate($value, array $data = null)
    {
        /*
         * We needed to serialize the value array in the form
         * (due to {@link \Zend\Form\Form:prepareBindValues()}),
         * so we must unserialize it here to hydrate the container with
         * an array.
         */
        $value = unserialize($value);

        /*
         * remove DOTS (,) from array keys because mongo fails on dots in key names.
         */
        $value = $this->filterArrayStrings($value, '.', '#dot#');

        return $value;
    }

    /**
     * Replaces strings in array keys and values recursively.
     *
     * @param array        $array   Target array
     * @param string|array $search  Search string(s)
     * @param string|array $replace Replacement string(s)
     *
     * @return array
     */
    protected function filterArrayStrings($array, $search, $replace)
    {
        $return = array();
        foreach ((array)$array as $key => $value) {
            $key = str_replace($search, $replace, $key);
            $value = is_array($value)
                ? $this->filterArrayStrings($value, $search, $replace)
                : str_replace($search, $replace, $value);

            $return[$key] = $value;
        }

        return $return;
    }
}
