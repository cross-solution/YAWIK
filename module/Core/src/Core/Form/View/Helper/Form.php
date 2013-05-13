<?php

namespace Core\Form\View\Helper;

use Zend\Form\FormInterface;
use Zend\Form\View\Helper\Form as ZendForm;

class Form extends ZendForm
{
    protected $extractFieldsets = true;
    protected $baseFieldsetName;
    protected $fieldsetOrder = array(
        '__base__',
        '__fieldsets__',
        'buttons'
    );
    
    public function render(FormInterface $form)
    {
        $baseFieldsetName = $this->getBaseFieldsetName();
        if (!$this->extractFieldsets() || !$baseFieldsetName) {
            return parent::render();
        }
        
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }
        
        
        $fieldsets = array();
        
        $baseFieldsetNameLength = strlen($baseFieldsetName);
        $baseFieldset = $form->get($this->getBaseFieldsetName());
        foreach ($baseFieldset->getIterator() as $elementOrFieldset):
            if ($elementOrFieldset instanceOf \Zend\Form\FieldsetInterface) {
                $fieldsetName = substr($elementOrFieldset->getName(), $baseFieldsetNameLength + 1, -1);
                $fieldsets[$fieldsetName] = $elementOrFieldset;
                $baseFieldset->remove($fieldsetName);
        }
        endforeach;
        
        $renderer = $this->getView()->plugin('formcollection');
        $markup = '';
        foreach ($this->getFieldsetOrder() as $fieldset) {
            switch ($fieldset) {
                case '__base__':
                    $markup .= $renderer($baseFieldset);
                    break;
                
                case '__fieldsets__':
                    foreach ($fieldsets as $fs) {
                        $markup .= $renderer($fs);
                    }
                    break;
                
                default:
                    if ($form->has($fieldset)) {
                        $markup .= $renderer($form->get($fieldset));
                    } else if (isset ($fieldsets[$fieldsets])) {
                        $markup .= $renderer($fieldsets[$fieldset]);
                    
                    }
                    break;
                    
            }
        }
        return $this->openTag($form) . $markup . $this->closeTag();
    }
    
    public function setFieldsetOrder(array $order)
    {
        $this->fieldsetOrder = $order;
        return $this;
    }
    
    public function getFieldsetOrder()
    {
        return $this->fieldsetOrder;
    }
    
    public function setExtractFieldsets($flag)
    {
        $this->extractCollections = (bool) $flag;
        return $this;
    }
    
    public function extractFieldsets()
    {
        return $this->extractFieldsets;
    }
    
    public function setBaseFieldsetName($name)
    {
        $this->baseFieldsetName = $name;
        return $this;
    }
    
    public function getBaseFieldsetName()
    {
        return $this->baseFieldsetName;
    }
    
    public function __invoke(FormInterface $form=null, $baseFieldset=null)
    {
        if (null === $form) {
            return $this;
        }
        
        $this->setBaseFieldsetName($baseFieldset);
        return $this->render($form);
    }
}
