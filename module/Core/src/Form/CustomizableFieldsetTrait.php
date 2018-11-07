<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form;

use Core\Options\FieldsetCustomizationOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
trait CustomizableFieldsetTrait
{
    /**
     * The customization options.
     *
     * @var FieldsetCustomizationOptions
     */
    protected $customizationOptions;

    public function setCustomizationOptions(FieldsetCustomizationOptions $options)
    {
        $this->customizationOptions = $options;

        return $this;
    }

    /**
     * @return \Core\Options\FieldsetCustomizationOptions
     */
    public function getCustomizationOptions()
    {
        if (!isset($this->customizationOptions)) {
            $this->setCustomizationOptions(new FieldsetCustomizationOptions());
        }

        return $this->customizationOptions;
    }

    protected function addElementOrFieldset($elementOrFieldset, array $flags = array())
    {
        if (!is_array($elementOrFieldset) || !$this->customizationOptions) {
            /** @noinspection PhpUndefinedClassInspection */
            return parent::add($elementOrFieldset, $flags);
        }

        if (isset($elementOrFieldset['name'])) {
            $name = $elementOrFieldset['name'];
        } elseif (isset($flags['name'])) {
            $name = $flags['name'];
        } else {
            /** @noinspection PhpUndefinedClassInspection */
            return parent::add($elementOrFieldset, $flags);
        }

        /* @var FieldsetCustomizationOptions $customOpts */
        $customOpts = $this->getCustomizationOptions();

        if (!$customOpts->isEnabled($name)) {
            return $this;
        }

        $elementOrFieldset = ArrayUtils::merge($elementOrFieldset, $customOpts->getFieldOptions($name));
        $flags             = ArrayUtils::merge($flags, $customOpts->getFieldFlags($name));

        /** @noinspection PhpUndefinedClassInspection */
        return parent::add($elementOrFieldset, $flags);
    }

    public function add($elementOrFieldset, array $flags = array())
    {
        return $this->addElementOrFieldset($elementOrFieldset, $flags);
    }

    protected function mergeInputFilterSpecifications(array $specification)
    {
        /* @var FieldsetCustomizationOptions $customOpts */
        $customOpts = $this->getCustomizationOptions();

        foreach ($customOpts->getFieldNames() as $name) {
            if (!isset($specification[$name])) {
                $specification[$name] = [];
            }

            $specification[$name] = ArrayUtils::merge(
                $specification[$name],
                $customOpts->getFieldInputSpecification($name)
            );
        }

        return $specification;
    }

    protected function getDefaultInputFilterSpecification()
    {
        return [];
    }

    public function getInputFilterSpecification()
    {
        return $this->mergeInputFilterSpecifications($this->getDefaultInputFilterSpecification());
    }
}
