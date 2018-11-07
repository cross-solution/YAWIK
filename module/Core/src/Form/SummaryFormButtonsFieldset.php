<?php

namespace Core\Form;

/**
 * Class SummaryFormButtonsFieldset
 *
 * @package Core\Form
 */
class SummaryFormButtonsFieldset extends ButtonsFieldset
{
    protected $formId;

    /**
     * Initialize the Buttons of a summary form element.
     */
    public function init()
    {
        $this->setName('buttons');
        if (!isset($this->options['render_summary'])) {
            $this->options['render_summary'] = false;
        }
        $this->setAttribute('class', 'text-right');
        
        $this->add(
            array(
            //'type' => 'Button',
            'type' => 'Core/Spinner-Submit',
            'name' => 'submit',
            'options' => array(
                'label' => /*@translate*/ 'Save',
            ),
            'attributes' => array(
                'id' => 'submit',
                'type' => 'submit',
                'value' => 'Save',
                'class' => 'sf-submit btn btn-primary btn-xs'
            ),
            )
        );
        
        $this->add(
            array(
            'type' => 'Button',
            'name' => 'cancel',
            'options' => array(
                'label' => /*@translate*/ 'Cancel',
            ),
            'attributes' => array(
                'id' => 'cancel',
                'type' => 'reset',
                'value' => 'Cancel',
                'class' => 'sf-cancel btn btn-default btn-xs'
            ),
            )
        );
    }

    /**
     * Set Options
     *
     * @param array|\Traversable $options
     * @return $this
     */
    public function setOptions($options)
    {
        if (!isset($options['render_summary'])) {
            $options['render_summary'] = isset($this->options['render_summary'])
                                       ? $this->options['render_summary']
                                       : false;
        }
        
        parent::setOptions($options);
        
        if (isset($options['form_id'])) {
            $this->setFormId($options['form_id']);
        }
        
        return $this;
    }

    /**
     * Set Attribute
     *
     * @param string $key
     * @param mixed  $value
     * @return \Zend\Form\Element|\Zend\Form\ElementInterface
     */
    public function setAttribute($key, $value)
    {
        if ('id' == $key) {
            $this->setFormId($value);
        }
        return parent::setAttribute($key, $value);
    }

    /**
     * Set the Form identifier
     *
     * @param $formId
     * @return $this
     */
    public function setFormId($formId)
    {
        $this->formId = $formId . '-';
       
        foreach ($this as $button) {
            $button->setAttribute('id', $this->formId . $button->getAttribute('id'));
        }
        return $this;
    }

    /**
     * Gets the form identifier
     *
     * @return string
     */
    public function getFormId()
    {
        return substr($this->formId, 0, -1);
    }
}
