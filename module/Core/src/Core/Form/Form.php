<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Form;

use Zend\Form\Form as ZendForm;
use Zend\Form\FieldsetInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputProviderInterface;
use Core\Entity\Hydrator\EntityHydrator;

class Form extends ZendForm implements DescriptionAwareFormInterface
{
    
    protected $params;
    
    protected $isDescriptionsEnabled = false;
    
    public function setIsDescriptionsEnabled($flag)
    {
        $this->isDescriptionsEnabled = (bool) $flag;
        return $this;
    }
    
    public function isDescriptionsEnabled()
    {
        return $this->isDescriptionsEnabled;
    }
    
    public function setDescription($description)
    {
        $this->options['description'] = $description;
        return $this;
    }
    
    public function getHydrator() {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->addHydratorStrategies($hydrator);
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function setOptions($options)
    {
        $desc = isset($this->options['description']) ? $this->options['description'] : null;
        
        parent::setOptions($options);
        
        if (isset($options['enable_descriptions'])) {
            $this->setIsDescriptionsEnabled($options['enable_descriptions']);
        }
        
        if (!isset($options['description']) && null !== $desc) {
            $this->options['description'] = $desc;
        }
    }
    
    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
        return $this;
    }
    
    public function setParam($key, $value)
    {

        if ($this->has($key)) {
            $this->get($key)->setValue($value);
        } else {
            $this->add(array(
                'type' => 'hidden', 
                'name' => $key, 
                'attributes' => array(
                    'value' => $value
                )
            ));
        }
        return $this;
    }
    
    protected function addHydratorStrategies($hydrator)
    { }
    
    public function addClass($spec) {
        $class = array();
        if ($this->hasAttribute('class')) {
            $class = $this->getAttribute('class');
        }
        if (!is_array($class)) {
            $class = explode( ' ', $class);
        }
        if (!in_array($spec, $class)) {
            $class[] = $spec;
        }
        $this->setAttribute('class', implode(' ',$class));
        return $this;
    }
    
    public function setValidate() {
        return $this->addClass('validate');
    }
    
    public function isValid()
    {
        $isValid = parent::isValid();
        if ($isValid) {
            if (is_object($this->object)) {
                $this->bind($this->object);
            } else {
                $filter = $this->getInputFilter();
                $this->setData($filter->getValues());
            }
        }
        
        return $isValid;
    }
    
}