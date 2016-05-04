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

use Traversable;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\Fieldset;
use Zend\Form\FieldsetInterface;
use Zend\Form\Form as ZfForm;
use Zend\Json\Json;
use Zend\Stdlib\ArrayUtils;

/**
 * Simple Form for result list filtering.
 *
 * Should be used with the searchForm view helper.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.25
 */
class TextSearchForm extends ZfForm
{
    protected $elementsFieldset = 'Core/TextSearch/Elements';
    protected $buttonsFieldset  = 'Core/TextSearch/Buttons';

    public function setOptions($options)
    {
        parent::setOptions($options);

        $options = $this->options; // assure array

        if (isset($options['elements_fieldset'])) {
            $this->elementsFieldset = $options['elements_fieldset'];
        }

        if (isset($options['buttons_fieldset'])) {
            $this->buttonsFieldset = $options['buttons_fieldset'];
        }

        return $this;
    }

    public function setSearchParams($params)
    {
        if ($params instanceOf \Traversable) {
            $params = ArrayUtils::iteratorToArray($params);
        }

        $params = Json::encode($params);
        $this->setAttribute('data-search-params', $params);

        return $this;
    }

    public function init()
    {
        $this->setAttributes([
                                 'class' => 'form-inline search-form',
                                 'data-handle-by' => 'script',
                                 'method' => 'get',
        ]);
        $elements = $this->elementsFieldset;

        if (!is_object($elements)) {
            $elements = [ 'type' => $elements, 'options' => $this->getOption('elements_options') ?: [] ];

        }

        $this->add($elements, [ 'name' => 'elements' ]);

        $buttons = $this->buttonsFieldset;

        if (!is_object($buttons)) {
            $buttons = [ 'type' => $buttons ];
        }

        $this->add($buttons, [ 'name' => 'buttons' ]);
    }

    public function add($elementOrFieldset, array $flags = [])
    {
        $name = null;
        if (isset($flags['name'])) {
            $name = $flags['name'];

        } else if (is_array($elementOrFieldset) && isset($elementOrFieldset['name'])) {
            $name = $elementOrFieldset['name'];

        } else if (is_object($elementOrFieldset)) {
            $name = $elementOrFieldset->getName();

        }

        if (!$name || !in_array($name, [ 'elements', 'buttons' ])) {
            throw new \InvalidArgumentException('Invalid named element. You can only add elements named "elements" or "buttons".');
        }

        parent::add($elementOrFieldset, $flags);

        $element = $this->get($name);

        if (!($element instanceOf TextSearchFormFieldset || $element instanceOf TextSearchFormButtonsFieldset)) {
            throw new \UnexpectedValueException(
                'Elements added to TextSearchForm must be fieldsets which extends from TextSearchForm[Buttons]Fieldset.'
            );
        }

        return $this;
    }


    public function getElements()
    {
        return $this->get('elements');
    }

    public function getButtons()
    {
        return $this->get('buttons');
    }
}