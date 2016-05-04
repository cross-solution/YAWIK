<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form;

use Traversable;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\Fieldset;
use Zend\Form\FieldsetInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class TextSearchFormFieldset extends Fieldset
{

    public function setButtonElement($name)
    {
        $this->setOption('button_element', $name);
    }

    public function getButtonElement()
    {
        $name = $this->getOption('button_element');
        if (null === $name) {
            $name = 'text';
            $this->setButtonElement($name);
        }

        return $name;
    }

    public function setColumnMap($map)
    {
        $this->setOption('column_map', $map);
        return $this;
    }

    public function getColumnMap()
    {
        $map = $this->getOption('column_map');

        if (null === $map) {
            $map = [];
            foreach ($this as $element) {
                $col = $element->getOption('span');
                if (null !== $col) {
                    $map[$element->getName()] = $col;
                }
            }

            $this->setOption('column_map', $map);
        }

        return $map;
    }

    public function setOptions($options)
    {
        parent::setOptions($options);

        $options = $this->options; // assure array

        $hasTextElement = $this->has('text');

        if (isset($options['placeholder']) && $hasTextElement) {
            $this->get('text')->setAttribute('placeholder', $options['placeholder']);
        }

        if (isset($options['span']) && $hasTextElement) {
            $this->get('text')->setOption('span', $options['span']);
        }

        return $this;
    }


    public function init()
    {
        $this->addTextElement(
            /*@translate*/ 'Search',
            $this->getOption('placeholder') ?: /*@translate*/ 'Search query',
            $this->getOption('span') ?: 12
        );
    }

    protected function addTextElement($label, $placeholder = 'Search query', $span = 12)
    {
        return $this->add([
                              'type' => 'Text',
                              'name' => 'text',
                              'options' => [
                                  'label' => $label,
                                  'span' => $span,
                              ],
                              'attributes' => [
                                  'placeholder' => $placeholder,
                              ],
                          ]);
    }

    public function add($elementOrFieldset, array $flags = [])
    {
        if (is_array($elementOrFieldset)) {
            if (!isset($elementOrFieldset['options']['use_formrow_helper'])) {
                $elementOrFieldset['options']['use_formrow_helper'] = false;
            }
            $class = isset($elementOrFieldset['attributes']['class']) ? $elementOrFieldset['attributes']['class'] : '';
            $elementOrFieldset['attributes']['class'] = "$class form-control";

            if (isset($elementOrFieldset['options']['is_button_element'])
                && $elementOrFieldset['options']['is_button_element']
            ) {
                $this->setButtonElement(
                     isset($flags['name']) ? $flags['name'] : $elementOrFieldset['name']
                );
            }

        } else if ($elementOrFieldset instanceOf ElementInterface) {
            $useFormRowHelper = $elementOrFieldset->getOption('use_formrow_helper');
            if (null === $useFormRowHelper) {
                $elementOrFieldset->setOption('use_formrow_helper', false);
            }
            $class = $elementOrFieldset->hasAttribute('class') ? $elementOrFieldset->getAttribute('class') : '';
            $elementOrFieldset->setAttribute('class', "$class form-control");

            if (true === $elementOrFieldset->getOption('is_button_element')) {
                $this->setButtonElement($elementOrFieldset->getName());
            }
        }

        return parent::add($elementOrFieldset, $flags);
    }
}