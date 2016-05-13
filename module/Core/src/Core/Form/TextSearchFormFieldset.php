<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Core\Form;

use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\Fieldset;

/**
 * Fieldset for elements in a TextSearchForm
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.25
 */
class TextSearchFormFieldset extends Fieldset
{

    /**
     * Gets the name of the button element.
     *
     * @return string
     */
    public function getButtonElement()
    {
        $name = $this->getOption('button_element');
        if (null === $name) {
            $name = 'text';
            $this->setButtonElement($name);
        }

        return $name;
    }

    /**
     * Sets the name of the button element.
     *
     * This is the element, the searchForm view helper should
     * render the buttons in an input button group addon.
     *
     * @param string $name
     *
     * @return self
     */
    public function setButtonElement($name)
    {
        return $this->setOption('button_element', $name);
    }

    /**
     * Sets the column map.
     *
     * @param array $map
     *
     * @see \Core\Form\View\Helper\SearchForm
     * @return self
     */
    public function setColumnMap($map)
    {
        $this->setOption('column_map', $map);

        return $this;
    }

    /**
     * Gets the column map.
     *
     * Generates the column map from the element options,
     * if none is set.
     *
     * @return array
     */
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

        if ($this->has('text')) {
            $textElement = $this->get('text');

            if (isset($options['text_placeholder'])) {
                $textElement->setAttribute('placeholder', $options['text_placeholder']);
            }

            if (isset($options['text_span'])) {
                $textElement->setOption('span', $options['text_span']);
            }

            if (isset($options['text_label'])) {
                $textElement->setLabel($options['text_label']);
            }
        }

        return $this;
    }


    public function init()
    {
        $this->addTextElement(
             $this->getOption('text_label')
                 ? : /*@translate*/ 'Search',
             $this->getOption('text_placeholder')
                 ? : /*@translate*/ 'Search query',
             $this->getOption('text_span') ? : 12
        );
    }

    public function add($elementOrFieldset, array $flags = [])
    {
        if (is_array($elementOrFieldset)) {
            $class                                    =
                isset($elementOrFieldset['attributes']['class']) ? $elementOrFieldset['attributes']['class'] : '';
            $elementOrFieldset['attributes']['class'] = "$class form-control";

            if (isset($elementOrFieldset['options']['is_button_element'])
                && $elementOrFieldset['options']['is_button_element']
            ) {
                $this->setButtonElement(
                     isset($flags['name']) ? $flags['name'] : $elementOrFieldset['name']
                );
            }

        } else if ($elementOrFieldset instanceOf ElementInterface) {
            $class = $elementOrFieldset->hasAttribute('class') ? $elementOrFieldset->getAttribute('class') : '';
            $elementOrFieldset->setAttribute('class', "$class form-control");

            if (true === $elementOrFieldset->getOption('is_button_element')) {
                $this->setButtonElement($elementOrFieldset->getName());
            }
        }

        return parent::add($elementOrFieldset, $flags);
    }

    /**
     * Adds the search text element.
     *
     * @param string $label
     * @param string $placeholder
     * @param int    $span
     *
     * @return Fieldset|\Zend\Form\FieldsetInterface
     */
    protected function addTextElement($label, $placeholder = 'Search query', $span = 12)
    {
        return $this->add([
                              'type'       => 'Text',
                              'name'       => 'text',
                              'options'    => [
                                  'label' => $label,
                                  'span'  => $span,
                              ],
                              'attributes' => [
                                  'placeholder' => $placeholder,
                              ],
                          ]
        );
    }
}