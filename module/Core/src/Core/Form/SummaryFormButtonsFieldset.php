<?php

namespace Core\Form;

class SummaryFormButtonsFieldset extends ButtonsFieldset
{
    
    protected $formId;
    
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

    public function setAttribute($key, $value)
    {
        if ('id' == $key) {
            $this->setFormId($value);
        }
        return parent::setAttribute($key, $value);
    }

    public function setFormId($formId)
    {
        $this->formId = $formId . '-';
       
        foreach ($this as $button) {
            $button->setAttribute('id', $this->formId . $button->getAttribute('id'));
        }
        return $this;
    }
   
    public function getFormId()
    {
        return substr($this->formId, 0, -1);
    }
}
