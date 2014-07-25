<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
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

class Form extends ZendForm
{
    
    protected $params;
    
    public function getHydrator() {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->addHydratorStrategies($hydrator);
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
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
    
}