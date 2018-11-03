<?php
/**
 * YAWIK
 *
 * @filesource
 * @license   MIT
 * @copyright 2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Settings\Form\Element;

use Core\Form\Container;
use Core\Form\DisableCapableInterface;
use Core\Form\DisableElementsCapableInterface;
use Core\Form\Element\Checkbox;
use Core\Form\Element\ViewHelperProviderInterface;
use Zend\Form\Element;
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputProviderInterface;

/**
 * Element to configure disabled form elements.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class DisableElementsCapableFormSettings extends Element implements ViewHelperProviderInterface, InputProviderInterface, ElementPrepareAwareInterface
{
    /**
     * All generated checkboxes.
     * Format: [target form element name] => Checkbox|array([element name] => Checkbox | ...
     *
     * @var array
     */
    protected $checkboxes;

    /**
     * The target form.
     *
     * @var \Zend\Form\FormInterface
     */
    protected $form;

    /**
     * View helper service name.
     * Needed by {@link ViewHelperProviderInterface}
     *
     * @var string
     */
    protected $viewHelper = 'Settings/FormDisableElementsCapableFormSettings';

    public function getViewHelper()
    {
        return $this->viewHelper;
    }

    public function setViewHelper($helper)
    {
        $this->viewHelper = $helper;

        return $this;
    }

    /**
     * Gets the generated checkboxes array.
     *
     * @return array|null
     */
    public function getCheckboxes()
    {
        return $this->checkboxes;
    }

    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['form'])) {
            $this->setForm($options['form']);
        }
    }

    /**
     * {@inheritDoc}
     * Sets the "checked"-Attributes for all generated checkboxes according to the
     * respectively value.
     *
     * @uses setCheckboxesAttributes
     */
    public function setValue($value)
    {
        parent::setValue($value);
        $this->setCheckboxesAttributes($value, $this->checkboxes);

        return $this;
    }

    /**
     * Sets checked attributes to the checkboxes according to value.
     * That means: for each checkbox which key exists in the value array
     * the checked attribute is set to <i>false</i>.
     *
     * @internal Recursive method.
     *
     * @param array $value
     * @param array $checkboxes
     */
    protected function setCheckboxesAttributes($value, $checkboxes)
    {
        foreach ($value as $name => $spec) {
            if (is_numeric($spec)) {
                $spec = $name;
                $name = 0;
            }
            if (is_numeric($name)) {
                if (isset($checkboxes[$spec])) {
                    /* @var $box Checkbox */
                    $box = is_array($checkboxes[$spec])
                        ? $checkboxes[$spec]['__all__']
                        : $checkboxes[$spec];
                    $box->setAttribute('checked', false);
                }
                continue;
            }
            $this->setCheckboxesAttributes($spec, $checkboxes[$name]);
        }
    }

    /**
     * Gets the target form.
     *
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Sets the target form or form container.
     *
     * @param \Zend\Form\FormInterface|\Core\Form\Container $formOrContainer
     *
     * @return self
     * @throws \InvalidArgumentException if invalid form type is passed.
     */
    public function setForm($formOrContainer)
    {
        if (!$formOrContainer instanceof FormInterface
            && !$formOrContainer instanceof Container
        ) {
            throw new \InvalidArgumentException('Parameter must be either of type "\Zend\Form\FormInterface" or "\Core\Form\Container"');
        }

        $this->form = $formOrContainer;
        $this->generateCheckboxes();

        return $this;
    }

    public function getInputSpecification()
    {
        return array(
            'name'     => $this->getName(),
            'required' => true,
            'filters'  => array(
                array(
                    'name' => \Settings\Form\Filter\DisableElementsCapableFormSettings::class,
                ),
            ),
        );
    }

    public function prepareElement(FormInterface $form)
    {
        $name = $this->getName();

        $this->prepareCheckboxes($this->checkboxes, $name);
    }

    /**
     * Prepares the checkboxes prior to the rendering.
     *
     * @internal This method is called recursivly for each array level.
     *
     * @param array $boxes
     * @param       $prefix
     */
    protected function prepareCheckboxes(array $boxes, $prefix)
    {
        foreach ($boxes as $box) {
            if (is_array($box)) {
                $this->prepareCheckboxes($box, $prefix);
            } else {
                /* @var $box Checkbox */
                $box->setName($prefix . $box->getName());
            }
        }
    }

    /**
     * Generates checkboxes based on the target form.
     *
     * @uses form
     * @uses buildCheckboxes()
     */
    protected function generateCheckboxes()
    {
        if (!$this->form) {
            return;
        }

        $this->checkboxes = $this->buildCheckboxes($this->form, '');
    }

    /**
     * Builds toggle checkboxes based on form elements.
     * Populates {@link checkboxes}
     *
     * @internal Method called recursively.
     *
     * @param FormInterface $form
     * @param string        $prefix element name prefix
     *
     * @return array
     */
    protected function buildCheckboxes($form, $prefix)
    {
        $return = array();
        foreach ($form as $element) {
            /* @var $element \Zend\Form\ElementInterface|DisableElementsCapableInterface|DisableCapableInterface */
            $name = $element->getName();
            $elementName = $prefix . '[' . $element->getName() . ']';
            $options = $element->getOption('disable_capable');
            $boxOptions = array(
                'long_label'  => isset($options['label']) ? $options['label'] : ($element->getLabel() ? : $name),
                'description' => isset($options['description']) ? $options['description']
                        : 'Toggle availability of this element in the form.',
            );

            if ($element instanceof DisableElementsCapableInterface) {
                if ($element->isDisableElementsCapable()) {
                    $return[$name] = $this->buildCheckboxes($element, $elementName);
                }
                if ($element->isDisableCapable()) {
                    $box = $this->createCheckbox($elementName . '[__all__]', $boxOptions);
                    $box->setAttribute('checked', true);
                    $return[$name]['__all__'] = $box;
                }
                continue;
            }
            if (($element instanceof DisableCapableInterface && $element->isDisableCapable())
                || false !== $element->getOption('is_disable_capable')
            ) {
                $box = $this->createCheckbox($elementName, $boxOptions);
                $box->setAttribute('checked', true);
                $return[$name] = $box;
            }
        }

        return $return;
    }

    /**
     * Creates a toggle checkbox.
     *
     * @param string $name
     * @param array  $options
     *
     * @return Checkbox
     */
    protected function createCheckbox($name, $options)
    {
        $box = new Checkbox($name, $options);
        $box->setAttribute('checked', true)
            ->setAttribute(
                'id',
                preg_replace(
                    array('~\[~', '~\]~', '~--+~', '~-$~'),
                    array('-', '', '-', ''),
                    $name
                )
            );

        return $box;
    }
}
