<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright 2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Settings\Form\Filter;

use Zend\Filter\FilterInterface;

/**
 * Filter to convert element value to config value.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class DisableElementsCapableFormSettings implements FilterInterface
{
    /**
     * Filters the data array format from the form element to the config array format.
     *
     * @internal Serializes the config array due to code in \Zend\Form\Form::prepareBindData
     *
     * @param  array $value
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public function filter($value)
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Value must be an array.');
        }

        return serialize($this->convert($value));
    }

    /**
     * Converts an element value array to config array recursively.
     *
     * @param array $value
     *
     * @return array
     */
    protected function convert(array $value)
    {
        $return = array();
        foreach ($value as $name => $elements) {
            if (is_array($elements)) {
                // We have a checkbox hit for a subform/fieldset,
                if (isset($elements['__all__'])) {
                    if ('0' == $elements['__all__']) {
                        // The whole subform/fieldset shall be disabled, so add it and continue.
                        $return[] = $name;
                        continue;
                    } else {
                        // We do not need the toggle all checkbox value anymore.
                        unset($elements['__all__']);
                    }
                }
                // recurse with the subform/fieldset element toggle checkboxes.
                $result = $this->convert($elements);

                if (count($result)) {
                    // Some elements on the subform shall be disabled, so add the result array.
                    $return[$name] = $result;
                }
                continue;
            }

            if ('0' == $elements) {
                // We have a disabled element, add it to the array.
                $return[] = $name;
            }
        }
        return $return;
    }
}
