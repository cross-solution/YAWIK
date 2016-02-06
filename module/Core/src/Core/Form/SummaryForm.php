<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core forms */
namespace Core\Form;

/**
 * Form which provides alternate rendering (summary).
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class SummaryForm extends BaseForm implements SummaryFormInterface
{
    
    /**
     * Which representation to render.
     *
     * @var string
     */
    protected $renderMode = self::RENDER_ALL;
    
    /**
     * Hint, which representation to show in view
     *
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
        $this->add(
            array(
            'type' => 'SummaryFormButtonsFieldset'
            )
        );
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
}
