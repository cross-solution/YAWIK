<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core forms */ 
namespace Core\Form;

use Zend\Form\FieldsetInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\ReplaceableInputInterface;

/**
 * Form which provides alternate rendering (summary).
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class SummaryForm extends BaseForm implements SummaryFormInterface
{
    
    /**
     * Which representation to render.
     * @var string
     */
    protected $renderMode = self::RENDER_ALL;
    
    /**
     * Hint, which representation to show in view
     * @var string
     */
    protected $displayMode = self::DISPLAY_FORM;
    
    /**
     * {@inheritDoc}
     * @see \Core\Form\SummaryFormInterface::setRenderMode()
     */
    public function setRenderMode($mode)
    {
        $this->renderMode = $mode;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Form\SummaryFormInterface::getRenderMode()
     */
    public function getRenderMode()
    {
        return $this->renderMode;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Form\SummaryFormInterface::setDisplayMode()
     */
    public function setDisplayMode($mode)
    {
        $this->displayMode = $mode;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Form\SummaryFormInterface::getDisplayMode()
     */
    public function getDisplayMode()
    {
        return $this->displayMode;
    }
    
    /**
     * {@inheritDoc}
     * 
     * Uses {@link \Core\Form\SummaryFormButtonsFieldset} as buttons fieldset.
     * 
     * @see \Core\Form\BaseForm::addButtonsFieldset()
     */
    protected function addButtonsFieldset()
    {
        $this->add(array(
            'type' => 'SummaryFormButtonsFieldset'
        ));
    }
    
    /**
     * {@inheritDoc}
     * 
     * Sets render mode to {@link RENDER_SUMMARY}, if validation succeeded.
     * 
     * @see \Zend\Form\Form::isValid()
     */
    public function isValid()
    {
        $isValid = parent::isValid();
        if ($isValid) {
            $this->setRenderMode(self::RENDER_SUMMARY);
        }
        
        return $isValid;
    }

    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['display_mode'])) {
            $this->setDisplayMode($options['display_mode']);
        }
    }

    /**
     * for all Form, which use a Class for element-Spezification,
     * set the default for merging the specification
     *
     * you stell need to implement the spezifications as ReplaceableInputInterface
     *
     * @param InputFilterInterface $inputFilter
     * @param FieldsetInterface $fieldset
     */
    public function attachInputFilterDefaults(InputFilterInterface $inputFilter, FieldsetInterface $fieldset)
    {
        if ($fieldset instanceof InputFilterProviderInterface) {
            $fieldset->setPreferFormInputFilter(false);
        }
        parent::attachInputFilterDefaults($inputFilter, $fieldset);

        // Just for failure-detection,
        // if the fieldset is of the type InputFilterProviderInterface and it should merge the spezifications,
        // the inputfilter still needs to be an instance of ReplaceableInputInterface
        // since there is no general need for that, not being a ReplaceableInputInterface is not an error.
        // This here should be just a reminder, maybe for debugging
        if ($fieldset instanceof InputFilterProviderInterface) {
            if (!$this->getPreferFormInputFilter()) {
                // allMergedFilter contain all Filter, for which a merge has been applied
                $allMergedFilter = [];
                // this can not get fetched with getFilter() because it loops over this attachInput
                if (isset($this->filter)) {
                    foreach ($this->filter->getInputs() as $filter) {
                        if ($filter instanceof ReplaceableInputInterface) {
                            $allMergedFilter[] = $filter;
                        }
                    }
                }
            }
        }
    }
    
}
