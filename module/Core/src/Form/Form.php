<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Form;

use Core\Form\Hydrator\HydratorStrategyProviderInterface;
use Zend\Form\Form as ZendForm;
use Zend\Form\FieldsetInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\StrategyEnabledInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Core form.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Form extends ZendForm implements
    DescriptionAwareFormInterface,
                                       DisableElementsCapableInterface,
                                       FormParentInterface
{
    use EventManagerAwareTrait, HydratorStrategyAwareTrait;
    
    const EVENT_IS_VALID = 'validate';
    const EVENT_PREPARE = 'prepare';
    
    /**
     * Form parameters.
     * An array of key  => value pairs which are rendered as hidden fields.
     *
     * @var array
     */
    protected $params;

    /**
     * @var ZendForm
     */
    protected $parent;

    /**
     * Flag, if descriptions handling is enabled or not.
     *
     * @var bool
     */
    protected $isDescriptionsEnabled = false;

    /**
     * @param array|\Traversable|\Zend\Form\ElementInterface $elementOrFieldset
     * @param array                                          $flags
     *
     * @return $this
     */
    public function add($elementOrFieldset, array $flags = array())
    {
        parent::add($elementOrFieldset, $flags);
        if ($elementOrFieldset instanceof FormParentInterface) {
            $elementOrFieldset->setParent($this);
        }
        return $this;
    }

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsDescriptionsEnabled($flag)
    {
        $this->isDescriptionsEnabled = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDescriptionsEnabled()
    {
        return $this->isDescriptionsEnabled;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description, $params = null)
    {
        $this->options['description'] = $description;
        $this->options['description_params'] = $params;
        return $this;
    }

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsDisableCapable($flag)
    {
        $this->options['is_disable_capable'] = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisableCapable()
    {
        return isset($this->options['is_disable_capable'])
               ? $this->options['is_disable_capable']
               : true;
    }

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsDisableElementsCapable($flag)
    {
        $this->options['is_disable_elements_capable'] = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisableElementsCapable()
    {
        return isset($this->options['is_disable_elements_capable'])
               ? $this->options['is_disable_elements_capable']
               : true;
    }

    /**
     * @param array $map
     *
     * @return $this
     */
    public function disableElements(array $map)
    {
        if (!$this->isDisableElementsCapable()) {
            return $this;
        }

        foreach ($map as $key => $name) {
            if (is_numeric($key)) {
                $key = $name;
                $name = null;
            }

            if (!$this->has($key)) {
                continue;
            }

            $element = $this->get($key);

            if (null === $name) {
                if (($element instanceof DisableCapableInterface && $element->isDisableCapable())
                    || false !== $element->getOption('is_disable_capable')) {
                    $this->remove($element);
                }
                continue;
            }

            if ($element instanceof FieldsetInterface
                && $element instanceof DisableElementsCapableInterface
                && $element->isDisableElementsCapable()
            ) {
                $element->disableElements($name);
            }
        }
        return $this;
    }




    /**
     * @return \Zend\Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->addHydratorStrategies($hydrator);
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    /**
     * @param array|\Traversable $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $desc = isset($this->options['description']) ? $this->options['description'] : null;

        parent::setOptions($options);

        if (isset($options['enable_descriptions'])) {
            $this->setIsDescriptionsEnabled($options['enable_descriptions']);
        }

        if (!isset($options['description']) && null !== $desc) {
            $this->options['description'] = $desc;
        }
        return $this;
    }

    /**
     * Sets many form parameters at once.
     *
     * @param array $params
     *
     * @return self
     */
    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
        return $this;
    }

    /**
     * Sets a form parameter.
     *
     * @param string $key
     * @param string $value
     *
     * @return self
     */
    public function setParam($key, $value)
    {
        if ($this->has($key)) {
            $this->get($key)->setValue($value);
        } else {
            $this->add(
                [
                    'type'       => 'hidden',
                    'name'       => $key,
                    'attributes' => [
                        'value' => $value
                    ]
                ]
            );
        }
        return $this;
    }

    /**
     * Adds hydrator strategies to the default hydrator upon instanciation.
     *
     * @param \Zend\Hydrator\HydratorInterface $hydrator
     */
    protected function addHydratorStrategies($hydrator)
    {
    }

    /**
     * @param $spec
     *
     * @return $this
     */
    public function addClass($spec)
    {
        $class = array();
        if ($this->hasAttribute('class')) {
            $class = $this->getAttribute('class');
        }
        if (!is_array($class)) {
            $class = explode(' ', $class);
        }
        if (!in_array($spec, $class)) {
            $class[] = $spec;
        }
        $this->setAttribute('class', implode(' ', $class));
        return $this;
    }

    /**
     * @return Form
     */
    public function setValidate()
    {
        return $this->addClass('validate');
    }

    /**
     * {@inheritDoc}
     * Rebinds the bound object or sets data to the filtered values.
     */
    public function isValid()
    {
        $isValid = parent::isValid();
        if ($isValid) {
            if (is_object($this->object)) {
                $this->bind($this->object);
            } else {
                $filter = $this->getInputFilter();
                $this->setData($filter->getValues());
            }
        }
        
        $this->getEventManager()->trigger(static::EVENT_IS_VALID, $this, [
            'isValid' => $isValid
        ]);
        
        return $isValid;
    }

    public function getFormName()
    {
        $name = $this->getBaseFieldset()->getAttribute('name');
        if (empty($name)) {
            throw new \RuntimeException('missing name for form');
        }
        return $name;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }


    public function hasParent()
    {
        return isset($this->parent);
    }

    /**
     * reassurance, that no required inputFilter is set on a non-existing field, this mistake is otherwise hard to detect
     * there is still a lot to do on this issue
     * @param InputFilterInterface $inputFilter
     * @param FieldsetInterface $fieldset
     * @throws \RuntimeException
     */
    public function attachInputFilterDefaults(InputFilterInterface $inputFilter, FieldsetInterface $fieldset)
    {
        parent::attachInputFilterDefaults($inputFilter, $fieldset);
        /* @var $inputFilter \Zend\InputFilter\InputFilter */
        foreach ($inputFilter->getInputs() as $name => $input) {
            if (!$input instanceof InputFilterInterface) {
                /* @var $input \Zend\InputFilter\Input */
                $required = $input->isRequired();
                $inputExists = $fieldset->has($name);
                if (!$inputExists && $required) {
                    $fieldsetName = '';
                    if ($fieldset->hasAttribute('name')) {
                        $fieldsetName = 'in Fieldset "' . $fieldset->getAttribute('name') . '" ';
                    }
                    throw new \RuntimeException('input for "' . $name . '" ' . $fieldsetName . 'is required but a input-field with this name is not defined');
                }
            }
        }
    }

    /**
     * @see \Zend\Form\Form::prepare()
     */
    public function prepare()
    {
        if ($this->isPrepared) {
            return $this;
        }
        
        parent::prepare();
        $this->getEventManager()->trigger(static::EVENT_PREPARE, $this);
        
        return $this;
    }
}
