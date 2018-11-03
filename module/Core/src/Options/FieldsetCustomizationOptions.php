<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Options for fieldset customizations.
 *
 * Holds options for each element of the fieldset in {@link fields}.
 * This will then be merged into the element spec provided by the fieldset class.
 *
 * <pre>
 * $fields = [
 *      <fieldname> => [
 *          'enabled'      => BOOL,     // if FALSE, the element WILL NOT BE ADDED.
 *          'required'     => BOOL,     // Sets the attribute "required", and additionally
 *                                         hints to the input filter, that the field is required.
 *          'label'        => STRING,   // override ['options']['label']
 *          'priority'     => INT,      // override ['flags']['priority']
 *          'order'        => INT       // alias for 'priority',
 *
 *          'options'      => ARRAY     // will get merged to the 'options' key in the element spec.
 *          'attributes'   => ARRAY     // merged to the 'attributes' key in the element spec.
 *          'flags'        => ARRAY     // merged into the flags array provided in the method "add"
 *                                         of the fieldset.
 *          'input_filter' => ARRAY,    // merged into the input filter spec of the fieldset.
 * ];
 * </pre>
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo since 0.29
 */
class FieldsetCustomizationOptions extends AbstractOptions
{

    /**
     * Field specifications.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Set the fields options.
     *
     * @param array $fields
     *
     * @return self
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get the field options.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get the names of all customized fields.
     *
     * @return array
     */
    public function getFieldNames()
    {
        return array_keys($this->getFields());
    }

    /**
     * Has a field customized options?
     *
     * @param string $field
     *
     * @return bool
     */
    public function hasField($field)
    {
        return array_key_exists($field, $this->fields);
    }

    /**
     * Is a field enabled?
     *
     * Returns true, if
     * - There is no customized $field.
     * - The $field spec does not have the key 'enabled'
     * - The key 'enabled' is NOT TRUE.
     *
     * @param string $field
     *
     * @return bool
     */
    public function isEnabled($field)
    {
        return !isset($this->fields[$field]['enabled']) || (bool) $this->fields[$field]['enabled'];
    }

    /**
     * Get the field option array compatible with element spec.
     *
     * @param string $field
     *
     * @return array
     */
    public function getFieldOptions($field)
    {
        if (!$this->hasField($field)) {
            return [];
        }

        if (!isset($this->fields[$field]['__options__'])) {
            $this->fields[$field]['__options__'] = $this->copyArrayValues(
                $this->fields[$field],
                [
                    'attributes',
                    'options',
                    'label' => 'options',
                    'required' => ['key' => ['attributes','*'], 'value' => 'required', 'if' => true],
                    'type',
                ]
            );
        }

        return $this->fields[$field]['__options__'];
    }

    /**
     * Get the field flags array compatible with element flags.
     *
     * @param string $field
     *
     * @return array
     */
    public function getFieldFlags($field)
    {
        if (!$this->hasField($field)) {
            return [];
        }

        if (!isset($this->fields[$field]['__flags__'])) {
            $this->fields[$field]['__flags__'] = $this->copyArrayValues(
                $this->fields[$field],
                [
                    'flags' => [],
                    'order' => ['priority'],
                    'priority'
                ]
            );
        }

        return $this->fields[$field]['__flags__'];
    }

    /**
     * Get input filter spec for a field.
     *
     * @param string $field
     *
     * @return array
     */
    public function getFieldInputSpecification($field)
    {
        if (!$this->hasField($field)) {
            return [];
        }

        if (!isset($this->fields[$field]['__filter__'])) {
            $this->fields[$field]['__filter__'] = $this->copyArrayValues(
                $this->fields[$field],
                [
                    'input_filter' => [],
                    'required',
                ]
            );
        }

        return $this->fields[$field]['__filter__'];
    }

    /**
     * Copy specified keys from source to a new array.
     *
     * <pre>
     * $keys = [
     *      <name>,           // copy $source[<name>] to $target[<name>]
     *      <name> => <key>   // copy $source[<name>] to $target[<key][<name>]
     *      <name> => [<key>,<key>,..] // copy $source[<name>] to $target[<key>][<key>]..
     *      <name> => [
     *          'key' => <key>|[<key>,] // copy $source[<name>] to $target[<key]...
     *                                  // using '*' as <key> will be replaced by <name>
     *          'value' => <mixed>,     // do not use $source[<name>] but <mixed> as target value.
     *          'if' => <mixed>,        // only copy, if $source[<name>] equals <mixed>
     *      ]
     * ]
     * </pre>
     *
     * @param array $source
     * @param array $keys
     *
     * @return array
     */
    protected function copyArrayValues(array $source, array $keys)
    {
        $target = [];
        foreach ($keys as $key => $spec) {
            if (is_int($key)) {
                $key = $spec;
                $spec = null;
            }

            if (!array_key_exists($key, $source)) {
                continue;
            }

            if (null === $spec) {
                $target[$key] = $source[$key];
                continue;
            }

            if (is_string($spec)) {
                $target[$spec][$key] = $source[$key];
                continue;
            }

            if (isset($spec['if']) && $source[$key] !== $spec['if']) {
                continue;
            }

            if (isset($spec['key'])) {
                $targetKeys = is_array($spec['key']) ? $spec['key'] : [$spec['key']];
                $value = isset($spec['value']) ? $spec['value'] : $source[$key];
            } else {
                $targetKeys = $spec;
                $value = $source[$key];
            }

            $tmpTarget =& $target;
            foreach ($targetKeys as $targetKey) {
                if ('*' == $targetKey) {
                    $targetKey = $key;
                }
                if (!isset($tmpTarget[$targetKey])) {
                    $tmpTarget[$targetKey] = [];
                }
                $tmpTarget =& $tmpTarget[$targetKey];
            }

            $tmpTarget = $value;
        }

        return $target;
    }
}
