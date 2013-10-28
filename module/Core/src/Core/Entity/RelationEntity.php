<?php

namespace Core\Entity;

class RelationEntity implements EntityInterface, RelationInterface
{
    protected $entity = null;
    protected $callback;
    protected $params;
    
    public function __construct($callable, array $params = array())
    {
        $this->setCallback($callable, $params);
    }
    
    public function setCallback($callable, array $params = array())
    {
        if (!is_callable($callable)) {
            die (__METHOD__ . ': Callback must be callable.');
        }
        $this->callback = $callable;
        $this->setParams($params);
        return $this;
    }
    
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    public function getEntity()
    {
        $this->loadEntity();
        return $this->entity;
    }
    
    public function __call($method, $params)
    {
        $this->loadEntity();
        return call_user_func_array(array($this->entity, $method), $params);
    }
    /**
     * Sets a property through the setter method.
     *
     * An exception is raised, when no setter method exists.
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function __set($name, $value)
    {
        $this->loadEntity();
        return $this->entity->__set($name, $value);
    }
    
    /**
     * Gets a property through the getter method.
     *
     * An exception is raised, when no getter method exists.
     *
     * @param string $name
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function __get($name)
    {
        $this->loadEntity();
        return $this->entity->__get($name);
    }
    
    /**
     * Checks if a property exists and has a non-empty value.
     *
     * If the property is an array, the check will return, if this
     * array has items or not.
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        $this->loadEntity();
        return $this->entity->__isset($name);
    }
    
    public function isLoaded()
    {
        return null !== $this->entity;
    }
    
    protected function loadEntity()
    {
        if ($this->isLoaded()) {
            return;
        }
        
        $entity = call_user_func_array($this->callback, $this->params);
        
        if (!$entity instanceOf EntityInterface) {
            die (__METHOD__ . ': Callback must return EntityInterface.');
        }
        $this->entity = $entity;
        
    }
}