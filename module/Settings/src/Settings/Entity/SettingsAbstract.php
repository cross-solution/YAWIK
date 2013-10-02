<?php

/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Settings\Entity;
use Core\Entity\EntityResolverStrategyInterface;

class SettingsAbstract extends AwareEntity implements SettingsInterface, EntityResolverStrategyInterface {

    protected $config;
    
    /**
     * die Setting-Entity consists of Namespaces, which can 
     * subsequently be adressed
     * @param string $nameSpace
     * @return Settings\Entity\Settings
     */
    public function getByNamespace($nameSpace) {
        if (isset($this->data)) {
            if (isset($this->data[$nameSpace])) {
                if ($this->spawnsAsEntities) {
                    if (!$this->data[$nameSpace] instanceof $this) {
                        // transform array into the Entity-Class
                        if ($this instanceOf EntityResolverStrategyInterface) {
                            $entity = $this->getEntityByStrategy($nameSpace);
                            if (isset($entity)) {
                                $entity->setParent($this);
                            }
                        }
                        
                        if (!isset($entity)) {
                            $entity = new static($this);
                        }
                        $entity->setData($this->data[$nameSpace]);
                        $this->data[$nameSpace] = $entity;
                    }
                }
                return $this->data[$nameSpace];
            } else {
                // create a new entity
                $this->data[$nameSpace] = new static($this);
                return $this->data[$nameSpace];
            }
        }
        // the Entity is empty
        return Null;
    }

    public function getEntityByStrategy($nameSpace) {
        if ($this->getParent() instanceOf EntityResolverStrategyInterface) {
            return $this->getParent()->getEntityByStrategy($nameSpace);
        }
        return Null;
    }
    
    public function getGetter() {
        $getter = array();
        foreach (get_class_methods($this) as $method) {
            if (0 === strpos($method, 'get')) {
                $getMethod = substr($method, 3, strlen($method) - 1);
                if (!in_array($getMethod, array('ByNamespace', 'EntityByStrategy', 'Getter', 'ArrayCopy', 'Parent', 'Formular'))) {
                    $getter[] = substr($method, 3, strlen($method) - 1);
                }
            }
        }
        return $getter;
    }
    
    public function setConfig($config) {
        $this->config = $config;
    }
    
    public function getFormular($formular = Null) {
        if ($this->getParent() instanceOf EntityResolverStrategyInterface) {
            if (isset($this->config) && array_key_exists('formular', $this->config)) {
                $formular = $this->config['formular'];
            }
            // TODO, falls Formular leer ist, hier die Getter abfragen
            // ..
            return $this->getParent()->getFormular($formular);
        }
        return Null;
    }
}