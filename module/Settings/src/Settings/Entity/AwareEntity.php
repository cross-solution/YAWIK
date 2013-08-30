<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Settings\Entity;

use Core\Entity\AnonymEntityInterface;

class AwareEntity implements AnonymEntityInterface, \Iterator
{
    /**
     *
     * @var type 
     */
    protected $data;
    
    /**
     *
     * @var type 
     */
    protected $parent;
    
    /**
     *
     * @var type 
     */
    protected $accessWrite;
    
    /**
     *
     * @var type 
     */
    protected $spawnsAsEntities;
    
    /**
     *
     * @var type 
     */
    protected $changed;
    
    /**
     * Erase empty Data
     * @var Boole 
     */
    protected $trimData;
    
    public function __construct($parent) {
        $this->data = array();
        $this->parent = $parent;
        $this->accessWrite = True;
        $this->spawnsAsEntities = False;
        $this->changed = False;
        $this->trimData = True;
        return $this;
    }
    
    public function rewind()
    {
        reset($this->data);
    }
  
    public function current()
    {
        return current($this->data);
    }
  
    public function key() 
    {
        return key($this->data);
    }
  
    public function next() 
    {
        return next($this->data);
    }
  
    public function valid()
    {
        $key = key($this->data);
        return ($key !== NULL && $key !== FALSE);
    }
    
    public function setData($data) {
        $this->data = $data;
        return $this;
    }
    
    public function getArrayCopy() {
        return $this->arrayCopy($this->data);
    }
    
    protected function arrayCopy($array) {
          $result = array();
        foreach( $array as $key => $val ) {
            if( is_array( $val ) ) {
                $result[$key] = $this->arrayCopy( $val );
            } elseif ( is_object( $val ) ) {
                $result[$key] = clone $val;
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
    }
    
    public function populate($data) {
        // zuerst alle Daten löschen, die nicht 
        $hasChanged = False;
        foreach (array_keys($this->data) as $key) {
            if (!isset($data[$key])) {
                unset($this->data[$key]);
                $hasChanged = True;
            }
        }
        // Daten ersetzen
        foreach ($data as $key => $value) {
            if (isset($this->data[$key]) && $this->data[$key] instanceof $this) {
                // ToDo recursivle populate nested entities
            }
            if (isset($this->data[$key]) && is_array($this->data[$key]) && is_array($value)) {
                // arrays aren't matched
                $this->data[$key] = $value;
                $hasChanged = True;
            }
            else {
                if (!isset($this->data[$key]) || $this->data[$key] != $value) {
                    $this->data[$key] = $value;
                    $hasChanged = True;
                }
            }
        }
        if ($hasChanged) {
            $this->setChanged();
        }
        return $this;
    }
    
    public function spawnAsEntities($spawn = True) {
        $this->spawnsAsEntities = $spawn;
        return $this;
    }
    
    public function setAccessWrite($access = True, $recursive = True) {
        $this->accessWrite = $access;
        return $this;
    }
    
    /**
     * changing a subset should be marked up to the root
     * @param type $changed
     * @return \Settings\Entity\AwareEntity
     */
    public function setChanged($changed = True) {
        $this->changed = (bool) $changed;
        if ($this->parent instanceof AwareEntity) {
            $this->parent->setChanged($changed);
        }
        return $this;
    }
    
    public function hasChanged() {
        return $this->changed;
    }
    
    public function __set($key, $value) {
        if ($this->accessWrite) {
            if (isset($this->data)) {
                if (is_array($this->data)) {
                    if (isset($value) && (is_array($value) || !isset($this->data[$key]) || $value != $this->data[$key])) {
                        $this->data[$key] = $value;
                        $this->setChanged();
                    }
                }
            }
        }
        return $this;
    }
    
    public function __unset($key) {
        if ($this->accessWrite) {
            if (isset($this->data) && is_array($this->data) && isset($this->data[$key])) {
                unset($this->data[$key]);
                $this->setChanged();
            }
        }
        return $this;
    }
    
    public function __get($key) {
        if (isset($this->data)) {
            if (is_array($this->data)) {
                // hier die Spawns as Entity einbauen
                return isset($this->data[$key])?$this->data[$key]:Null;
            }
        }
        return Null;
    }
    
    public function toArray() {
        $return = array();
        foreach ($this->data as $key => $value) {
            if ($value instanceOf $this) {
                $value = $value->toArray();
            }
            $return[$key] = $value;
        }
        return $return;
    }
}