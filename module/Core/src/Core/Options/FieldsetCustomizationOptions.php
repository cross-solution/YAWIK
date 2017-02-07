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
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
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
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function getFieldNames()
    {
        return array_keys($this->getFields());
    }

    public function hasField($field)
    {
        return array_key_exists($field, $this->fields);
    }

    public function isEnabled($field)
    {
        return !isset($this->fields[$field]['enabled']) || (bool) $this->fields[$field]['enabled'];
    }

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
                $value = $spec['value'];
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