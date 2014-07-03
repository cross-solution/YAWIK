<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Core\Form;

use Zend\Form\Element;
use Zend\Form\FormInterface;
use Zend\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Core\Entity\EntityInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Container extends Element implements ServiceLocatorAwareInterface,
                                           \IteratorAggregate,
                                           \Countable
{
    protected $forms = array();
    protected $activeForms = array();
    protected $formElementManager;
    protected $entity;
    protected $params = array();
    
    public function __construct($name = null, $options = array())
    {
        // Normalize forms array.
        $this->setForms($this->forms);
        
        parent::__construct($name, $options);
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->formElementManager = $serviceLocator;
        return $this;
    }
    
    public function getServiceLocator()
    {
        return $this->formElementManager;
    }
    
    public function getIterator()
    {
        $self = $this;
        $forms = array_map(function($key) use ($self) {
            return $self->getForm($key);
        }, $this->activeForms);
        
        return new \ArrayIterator($forms);
    }
    
    public function count()
    {
        return count($this->activeForms);
    }
    
    public function setParams(array $params)
    {
        $this->params = $params;
        
        foreach ($this->forms as $form) {
            if (is_object($form)) {
                $form->setParams($params);
            }
        }
        return $this;
    }
    
    public function getParams()
    {
        return $this->params;
    }
    
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
        
        foreach ($this->forms as $form) {
            if (is_object($form)) {
                $form->setParam($key, $value);
            }
        }
        return $this;
    }
    
    public function getParam($key, $default = null)
    {
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }
    
    public function getForm($key)
    {
        if (!isset($this->forms[$key])) {
            return null;
        }
        
        $form = $this->forms[$key];
        if (is_object($form)) {
            return $form;
        } 
        $formInstance = $this->formElementManager->get($form['type']);
        $formName     = (($name = $this->getName())
                         ? $name . '.' : '')
                        . $form['name'];
        $formInstance->setName($formName);
        
        if ($entity = $this->getEntity()) {
            $mapProperty = isset($form['property']) ? $form['property'] : $key;
            if (true === $mapProperty) {
                $mapEntity = $entity;
            } else if (isset($entity->$mapProperty)) {
                $mapEntity = $entity->$mapProperty;
            } else {
                $mapEntity = null;
            }
            
            if ($mapEntity) {
                if ($formInstance instanceOf Container) {
                    $formInstance->setEntity($mapEntity); 
                } else {
                    $formInstance->bind($mapEntity);
                }
            }
        }
        
        $formInstance->setParams($this->getParams());
        $this->forms[$key] = $formInstance;
        return $formInstance;
    }
    
    public function setForm($key, $spec, $enabled = true)
    {
        if (!is_array($spec)) {
            $spec = array('type' => $spec, 'name' => $key);
        }
        if (!isset($spec['name'])) {
            $spec['name'] = $key;
        }
        
        $this->forms[$key] = $spec;
        if ($enabled) {
            $this->enableForm($key);
        } else if (true === $this->activeForms) {
            $this->activeForms = false;
        }
        return $this;
    }
    
    public function setForms(array $forms, $enabled = true)
    {
        foreach ($forms as $key => $spec) {
            if (is_array($spec) && isset($spec['enabled'])) {
                $currentEnabled = $spec['enabled'];
                unset($spec['enabled']);
            } else {
                $currentEnabled = $enabled;
            }
            $this->setForm($key, $spec, $currentEnabled);
        }
        return $this;
    }
    
    public function enableForm($key = null)
    {
        if (null === $key) {
            $this->activeForms = array_keys($this->forms);
            return $this;
        } 
        
        if (!is_array($key)) {
            $key = array($key);
        }
        
        foreach ($key as $k) {
            if (false !== strpos($k, '.')) {
                list($childKey, $childForm) = explode('.', $k, 2);
                $child = $this->getForm($childKey);
                $child->enableForm($childForm);
            } else {
                if (isset($this->forms[$k]) && !in_array($k, $this->activeForms)) {
                    $this->activeForms[] = $k;
                }
            }
        }
        
        return $this;
    }
    
    public function disableForm($key = null)
    {
        if (null === $key) {
            $this->activeForms = array();
            return $this;
        } 
        
        if (!is_array($key)) {
            $key = array($key);
        }
        
        foreach ($key as $k) {
            if (false !== strpos($k, '.')) {
                list($childKey, $childForm) = explode('.', $k, 2);
                $child = $this->getForm($childKey);
                $child->disableForm($childForm);
            } else {
                
            }
        }
        $this->activeForms = array_filter($this->activeForms, function ($item) use ($key) {
            return !in_array($item, $key);
        });
        
        return $this;
    }
    
    public function validateForm($key, $data)
    {
        if (false !== strpos($key, '.'))
        $form = $this->getForm($key);
        $form->setData($data);
        if ($form->isValid()) {
            return true;
        } else {
            return $form->getMessages();
        }
    }
    
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
        return $this;
    }
    
    public function getEntity()
    {
        return $this->entity;
    }
}
